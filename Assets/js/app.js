
// Modal
function modal(modal_id, modal_btn_id) {

    $('#' + modal_btn_id).on('click', function () {
        $('#' + modal_id).css("display", "block");
        resetImageUploader();
        $('#' + modal_id + ' .error').html("");
    });

    $('.close, .close-btn').on('click', function () {

        $('#' + modal_id).css("display", "none");
        $('#' + modal_id + ' .modal-title').html("Add New Image");
        resetImageUploader();
        $('input').val("");
        $("#save-iamge-btn").removeData('id');
        $("#save-iamge-btn").removeAttr('data-id');
    });

    $(window).on('click', function (e) {
        if (e.target == document.getElementById(modal_id)) {
            $('#' + modal_id).css("display", "none");
            $('#' + modal_id + ' .modal-title').html("Add New Image");
            resetImageUploader();
            $('input').val("");
            $("#save-iamge-btn").removeData('id');
            $("#save-iamge-btn").removeAttr('data-id');
        }
    });
}

function closeModal(modal_id) {
    $('#' + modal_id + ' .close, .close-btn').click();
    $('#' + modal_id + ' .modal-title').html("Add New Image");
}


// Image Upload

document.querySelectorAll(".image-upload-input").forEach((inputElement) => {
    const imageUploadElement = inputElement.closest(".image-upload");

    imageUploadElement.addEventListener("click", (e) => {
        inputElement.click();
    });

    inputElement.addEventListener("change", (e) => {
        if (inputElement.files.length) {
            updateThumbnail(imageUploadElement, inputElement.files[0]);
        }
    });

    imageUploadElement.addEventListener("dragover", (e) => {
        e.preventDefault();
        imageUploadElement.classList.add(".image-upload--over");
    });

    ["dragleave", "dragend"].forEach((type) => {
        imageUploadElement.addEventListener(type, (e) => {
            imageUploadElement.classList.remove(".image-upload--over");
        });
    });

    imageUploadElement.addEventListener("drop", (e) => {
        e.preventDefault();

        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(imageUploadElement, e.dataTransfer.files[0]);
        }

        imageUploadElement.classList.remove(".image-upload--over");
    });
});

function updateThumbnail(imageUploadElement, file) {
    let thumbnailElement = imageUploadElement.querySelector(".image-upload-thumb");

    // First time - remove the text
    if (imageUploadElement.querySelector(".image-upload-text")) {
        imageUploadElement.querySelector(".image-upload-text").remove();
    }

    // First time - there is no thumbnail element, so lets create it
    if (!thumbnailElement) {
        thumbnailElement = document.createElement("div");
        thumbnailElement.classList.add("image-upload-thumb");
        imageUploadElement.appendChild(thumbnailElement);
    }

    thumbnailElement.dataset.label = file.name;

    // Show thumbnail for image files
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();

        reader.readAsDataURL(file);
        reader.onload = () => {
            thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            thumbnailElement.style.height = '300px';
        };
    } else {
        thumbnailElement.style.backgroundImage = null;
    }
}

function resetImageUploader() {
    $('.image-upload-thumb').remove();
    if (!$('.image-upload .image-upload-text').length) {
        $('.image-upload').append("<div class='image-upload-text'>Drop file here or Click to Upload</div>");
    }

    $('.image-upload-text').html("Drop file here or Click to Upload");
}