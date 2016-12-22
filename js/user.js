$(function () {
    $('#fileupload').fileupload({
        url: SITE_URL+"profile/upload_avatar",
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
        start:function () {
            $("#loading").show();
        },
        complete:function  () {
            $("#loading").hide();
        }
    });
});

function show_image (file) {
    var files = file.files;
    var file = files[0];      
    var img = document.getElementById("photo");            
    img.file = file;    
    var reader = new FileReader();

    reader.onload = (function(aImg) { 
        return function(e) { 
            aImg.src = e.target.result; 
        }; 
    })(img);

    reader.readAsDataURL(file);
}

function changeInfo(el){
    event.preventDefault();
    
    var $element = $("#"+el);
    var text = $element.text();

    $element.next().remove();
    $element.html("<label class='input'><input type='text' name='"+el+"' value='"+text+"' ></label>");
    $element.children().hide().slideDown('slow');

    $("#saveInfo").removeClass('hidden');
}