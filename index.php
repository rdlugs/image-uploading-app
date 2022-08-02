<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>

    <link rel="stylesheet" href="./Assets/css/app.css">
</head>

<body>
    <!-- Modal -->
    <div id="add-new-image-modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close">&times;</span>
            <div class="modal-header">
                <h3 class="modal-title">Add New Image</h3>
            </div>
            <div class="modal-body">
                <div>
                    <input type="text" class="input title" id="title" placeholder="Enter Title" />
                    <span class="error title"></span>
                </div>

                <div class="image-upload" style="margin-top: 20px;">
                    <span class="image-upload-text">Drop file here or Click to Upload</span>
                    <input type="file" accept="image/png,image/jpeg" id="image" class="image-upload-input" hidden />
                </div>
                <div>
                    <span class="error image"></span>
                </div>
            </div>
            <div class="container" style="margin-top: 50px;">
                <div class="justify-end">
                    <button class="add-new-image-btn btn btn-red close-btn">Cancel</button>
                    <button class="add-new-image-btn btn" id="save-iamge-btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container justify-content-center">
        <div class="table-container shadow">
            <div class="table-header">
                <h3>Images</h3>
                <button type="button" class="add-new-image-btn justify-end btn" id="add-new-image-btn">Add New</button>
            </div>

            <table>
                <thead>
                    <th>Title</th>
                    <th>Thumbnail</th>
                    <th>Filename</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </thead>
                <tbody id="image-table-body">

                </tbody>
            </table>
        </div>
    </div>

    <script src="./Assets/js/jquery.js"></script>
    <script src="./Assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            loadDatas();
            modal("add-new-image-modal", "add-new-image-btn");


            $('#save-iamge-btn').on('click', function(e) {

                let formData = new FormData();

                formData.append('file', $('#image')[0].files[0]);
                formData.append('title', $('#title').val());
                formData.append('method', 'insertorUpdateImage');

                if ($(this).data('id')) {
                    formData.append('id', $(this).data('id'));
                }

                $.ajax({
                    method: 'POST',
                    url: window.location.origin + "/api/image.php",
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#save-iamge-btn').attr('disabled', true).html('Saving...');
                    },
                    complete: function() {
                        $('#save-iamge-btn').attr('disabled', false).html('Save');
                    },
                    success: function(response) {
                        if (typeof response.errors == 'undefined') {
                            loadDatas();
                            closeModal('add-new-image-modal');
                        } else {
                            $('.error.title').html(response.errors.title);
                            $('.error.image').html(response.errors.file);
                        }
                    }
                });
            });
        });

        function loadDatas() {
            $.ajax({
                method: 'POST',
                url: window.location.origin + "/api/image.php",
                data: {
                    method: 'getAllDatas'
                },
                dataType: 'json',
                success: function(response) {
                    populateTable(response);
                }
            });
        }

        function populateTable(datas = []) {
            let table = $('#image-table-body');
            table.empty();

            if (datas.length > 0) {
                $.each(datas, function(i, v) {

                    let title = "<td>" + v.title + "</td>";
                    let filename = "<td>" + v.filename + "</td>";
                    let date_created = "<td>" + new Date(v.created_at).toLocaleDateString() + "</td>";

                    let image = $('<img />', {
                        src: window.location.origin + v.image,
                        style: 'width:200px;'
                    });

                    let imageLink = $('<a/>', {
                            href: window.location.origin + v.image,
                            target: '_blank'
                        })
                        .append(image);

                    let thumbnail = $("<td />", {
                        style: 'text-align:center;'
                    }).append(imageLink);

                    let deleteBtn = $('<button/>', {
                        text: 'Delete',
                        class: 'delete btn btn-red',
                        id: 'delete' + i,
                        click: function() {
                            deleteData(v.id);
                        }
                    });

                    let editBtn = $('<button/>', {
                        text: 'Edit',
                        class: 'edit btn',
                        id: 'edit-btn' + i,
                        style: 'margin-left:5px',
                        click: function() {
                            edit(v.id, this);
                        }
                    });

                    let actions = $('<td/>').append(deleteBtn).append(editBtn);

                    let tr = $('<tr>')
                        .append(title)
                        .append(thumbnail)
                        .append(filename)
                        .append(date_created)
                        .append(actions);

                    table.append(tr);
                    modal("add-new-image-modal", "edit-btn" + i);
                });
            } else {
                table.append("<tr><td colspan='5' style='text-align:center;'>Empty Table</td></tr>");
            }
        }

        function edit(id, elem) {

            $('#add-new-image-modal .modal-title').html("Edit Image");
            $('#save-iamge-btn').data('id', id);

            $.ajax({
                method: 'POST',
                url: window.location.origin + "/api/image.php",
                data: {
                    id: id,
                    method: 'getData'
                },
                dataType: 'json',
                success: function(response) {
                    $('#title').val(response.title);
                    $('.image-upload-text').html("Drop file or Click to Replace Image");
                }

            });
        }

        function deleteData(id) {

            $.ajax({
                method: 'POST',
                url: window.location.origin + "/api/image.php",
                data: {
                    id: id,
                    method: 'deleteData'
                },
                dataType: 'json',
                success: function(response) {
                    loadDatas();
                }

            });
        }
    </script>
</body>

</html>