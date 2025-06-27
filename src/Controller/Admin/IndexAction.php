<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\EcuSoftware;
use App\Repository\EcuSoftwareRepository;
use App\Repository\EcuSoftwareServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;

#[Route('/admin', name: 'admin', methods: [Request::METHOD_GET, Request::METHOD_POST])]
class IndexAction extends AbstractController
{
    public function __invoke(
        Request $request,
        EcuSoftwareRepository $ecuSoftwareRepository,
        EcuSoftwareServiceRepository $ecuSoftwareServiceRepository
    ): Response {
        $form = $this->createFormBuilder(null)
            ->add('ecu', TextType::class)
            ->add('software_type', TextType::class)
            ->add('software', TextType::class)
            ->add('file', FileType::class, [
                'constraints' => [
                    new File([
                        'mimeTypes' => ['application/octet-stream']
                    ])
                ]
            ])
            ->add('service', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $softwareId = $form->get('software')->getData();
            if ('any' === $form->get('software_type')->getData()) {
                $softwareId = $ecuSoftwareRepository
                    ->findOneBy(['version' => EcuSoftware::VERSION_ANY, 'ecu' => $form->get('ecu')->getData()])
                    ->getId();
            }

            $ecuSoftwareService = $ecuSoftwareServiceRepository->findOneBy([
                'ecuSoftware' => $softwareId,
                'service' => $form->get('service')->getData()
            ]);

            if (null === $ecuSoftwareService) {
                throw $this->createNotFoundException();
            }

            $file = $form->get('file')->getData();
            $content = $file->getContent();
            foreach ($ecuSoftwareService->getReplacement() as $hexAddress => $hexValue) {
                $content[hexdec((string)$hexAddress)] = hex2bin($hexValue);
            }

            return new Response($content, Response::HTTP_OK, [
                'Content-type' => $file->getMimeType(),
                'Content-Disposition' => sprintf(
                    'attachment; filename="%s%s.%s";',
                    basename($file->getClientOriginalName(), $file->getClientOriginalExtension()),
                    $ecuSoftwareService->getService()->getName(),
                    $file->getClientOriginalExtension()
                )
            ]);
        }

        return $this->render('admin/index.html.twig', ['form' => $form]);
    }
}
