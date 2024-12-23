var fileArea = document.getElementById('drop__zone');
var fileInput = document.getElementById('file__input');

fileArea.addEventListener('click', function() {
    fileInput.click();
});

fileArea.addEventListener('dragover', function(evt){
    evt.preventDefault();
});

fileArea.addEventListener('dragleave', function(evt){
    evt.preventDefault();
});

fileArea.addEventListener('drop', function(evt){
    evt.preventDefault();
    var files = evt.dataTransfer.files;
    fileInput.files = files;
    imagePreview('onChenge',files[0]);
});

function imagePreview(event, f = null) {
    var file = f;
    if(file === null){
        file = event.target.files[0];
    }
    var reader = new FileReader();
    var previewImage = document.getElementById("preview__image");
    var message = document.getElementById("message");
    message.style.display = '';

    if(previewImage != null) {
        fileArea.removeChild(previewImage);
    }

    reader.onload = function(event) {
        var img = document.createElement("img");
        img.setAttribute("src", reader.result);
        img.setAttribute("id", "preview__image");
        fileArea.appendChild(img);
        message.style.display = 'none';
    };

    reader.readAsDataURL(file);
}

function countLength( text, text__length ) {
    document.getElementById(text__length).innerHTML = text.replace(/[\n]/g, "aa").length + "/400 (最大文字数)";
}
