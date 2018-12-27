<!DOCTYPE html>
<html>
<head>
    <title>Articles</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <br/>
    <br/>
    <div align="right">
        <button type="button" name="add" id="add_data" class="btn btn-success btn-sm">Add</button>
    </div>
    <br/>

    <span id="form_output"></span>

    <table id="articles" class="table table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
    </table>
</div>

<div id="addDialog" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="article_form" id="article_form">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div id="nameDiv" class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" maxlength="50" required/>
                    </div>
                    <div id="descDiv" class="form-group">
                        <label for="description">Description</label>
                        <textarea rows="6" cols="50" name="description" id="description" class="form-control"
                                  maxlength="500" required></textarea>
                    </div>
                    <div id="imgDiv" class="form-group">
                        <label for="imageName">Image</label>
                        <input type="file" name="imageName" id="imageName" class="form-control" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="article_id" id="article_id" value=""/>
                    <input type="hidden" name="button_action" id="button_action" value="insert"/>
                    <input type="submit" name="submit" id="action" value="Add" class="btn btn-info"/>
                    <button type="button" id="dismiss" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#articles').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": '{{ url('index') }}',
            "columns": [
                {"data": "name"},
                {"data": "description"},
                {
                    "data": "imageName", orderable: false, searchable: false,
                    'render': function (data) {
                        return '<img src="images/' + data + '" style="height:100px;width:100px;" alt="' + data + '"/>';
                    }
                },
                {"data": "action", orderable: false, searchable: false}
            ]
        });

        $('#add_data').click(function () {
            removeChildImg('imgDiv');
            var articleId = document.getElementById('article_id');
            articleId.value = 0;

            $('#addDialog').modal('toggle');
            $('#article_form')[0].reset();
            $('#form_output').html('');
            $('#button_action').val('insert');
            $('#action').val('Add');
        });

        $('#dismiss').click(removeErrors);

        $('#article_form').on('submit', function (event) {
            event.preventDefault();
            var form = $('form')[0];
            var form_data = new FormData(form);
            $.ajax({
                url: "{{ route('articles.store') }}",
                method: "POST",
                data: form_data,
                contentType: false,
                processData: false,
                success: function (data) {
                    var responseData = JSON.parse(data);
                    console.log(responseData.test);
                    if (responseData.errorsCount > 0 && (Object.values(responseData.errors) !== "")) {
                        console.log((Object.values(responseData.errors)));
                        $.each(responseData.errors, function (key, value) {
                            var motherID = document.getElementById(key).parentElement.id;
                            removeChildSpan(motherID);
                            var mother = document.getElementById(motherID);
                            var spanTag = document.createElement("span");
                            spanTag.innerHTML = value;
                            spanTag.className = "text-danger";
                            mother.appendChild(spanTag);
                        })
                    }
                    else {
                        $('#form_output').html(responseData.successes);
                        setTimeout(function () {
                            $('#form_output').html('');
                        }, 5000);
                        $('#addDialog').modal('toggle');
                        removeErrors();
                        $('#articles').DataTable().ajax.reload();
                    }
                },
                error: function (error) {
                    console.log(JSON.stringify(error));
                    alert("There is a problem..");
                }
            })
        });
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr("id");
        $('#form_output').html('');
        $.ajax({
            url: "{{route('articles.fetchdata')}}",
            method: 'get',
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $('#name').val(data.name);
                $('#description').val(data.description);

                var imgDiv = document.getElementById('imgDiv');
                removeChildImg('imgDiv');
                var img = document.createElement('img');
                img.setAttribute('src', 'images/' + data.imageName);
                img.setAttribute('alt', data.imageName);
                img.setAttribute('height', '100px');
                img.setAttribute('width', '100px');
                imgDiv.appendChild(img);

                $('#article_id').val(id);
                $('#addDialog').modal('toggle');
                $('#action').val('Edit');
                $('#button_action').val('update');
            }
        })
    });

    function removeChildSpan(parentID) {
        var lastChild = document.getElementById(parentID).lastElementChild;
        if (lastChild.tagName === "SPAN") {
            var mother = document.getElementById(parentID);
            mother.removeChild(mother.lastChild);
        }
    }

    function removeChildImg(parentID) {
        var lastChild = document.getElementById(parentID).lastElementChild;
        if (lastChild.tagName === "IMG") {
            var mother = document.getElementById(parentID);
            mother.removeChild(mother.lastChild);
        }
    }

    function removeErrors() {
        //TODO: find better way;
        var nameDiv = document.getElementById("nameDiv");
        if (nameDiv.lastElementChild.tagName === "SPAN") {
            nameDiv.removeChild(nameDiv.lastChild);
        }

        var descDiv = document.getElementById("descDiv");
        if (descDiv.lastElementChild.tagName === "SPAN") {
            descDiv.removeChild(descDiv.lastChild);
        }

        var imgDiv = document.getElementById("imgDiv");
        if (imgDiv.lastElementChild.tagName === "SPAN") {
            imgDiv.removeChild(imgDiv.lastChild);
        }
    }
</script>
</body>
</html>
