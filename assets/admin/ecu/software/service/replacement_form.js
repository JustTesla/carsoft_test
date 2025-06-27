(function ($) {
    "use strict"; // Start of use strict

    $(document).ready(function () {

        $('#form_replacement').on('change', function (event) {
            var fileName = $(this).val();
            $(this).next('.custom-file-label').html(fileName);

            const reader = new FileReader();
            reader.addEventListener('load', (event) => {
                let buffer = '',
                    content = '',
                    lines = 0;
                for (let i = 0; i < event.target.result.length; i++) {
                    let char = event.target.result[i];
                    buffer += char;
                    if ("\n" == char) {
                        lines++;
                        if (lines < 6) {
                            content += buffer;
                        }
                        buffer = '';
                    }
                }

                content += "\n...\n\n" + buffer;

                $("#replacementFileContent").removeClass('d-none').find('.card-body pre').text(content);
            });
            reader.readAsText(event.target.files[0]);
        });
    });

})(jQuery);