(function($){
    $(function(){

        $(document).ready(function(){
            $('.modal').modal();
            $('.button-collapse').sideNav();
            $('.parallax').parallax();
            $('.materialboxed').materialbox();
            $('.collapsible').collapsible();
            $('select').material_select();

        });

        function validateFileType(){
            var fileName = document.getElementById("fileName").value;
            var idxDot = fileName.lastIndexOf(".") + 1;
            var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
            if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
                //TO DO
            }else {
                $('#fileName').val(null);
                alert("Lze přidat pouze obrázky .jpg, .png, .jpeg!");
            }

            if(typeof document.getElementById('fileName').files[0] !== 'undefined'){
                var maxSize = 10571520;
                var size = document.getElementById('fileName').files[0].size;
                var isOk = maxSize > size;

                if (!isOk) {
                    $('#fileName').val(null);
                    alert("Obrázek může mít maximálně 10MB");
                }
            }
        }


    }); // end of document ready
})(jQuery); // end of jQuery name space