<form id="files" type="multipart/form-data">
    Select files to upload:
    <?php for ($i = 0; $i < $data; $i++) {?>
    <div style="padding: 20px;">
        <input type="file" name="fileToUpload[<?=$i?>]" id="fileToUpload_<?=$i?>" accept="image/*">
        <label>Description</label>
        <input type="text" name="description[<?=$i?>]" id="description_<?=$i?>" pattern=".{1,60}" title="Max 60 characters">
    </div>
    <?php } ?>
    <input type="submit" value="Upload">
</form>

<script>
    let fileExtension = ['jpeg', 'jpg'];
    let maxFileSize = 100000;
    $("input[type=file]").change(function () {
        console.log($(this).files);
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
            $(this).val("");
            return false;
        }

        if ($(this)[0].files[0].size > maxFileSize) {
            alert("More than 200 kb");
            $(this).val("");
            return false;
        }
    });

    $("input[type=submit]").click(function () {
        event.preventDefault();
        var formData = new FormData($('#files')[0]);
        $.ajax({
            type: 'POST',
            url: '/file/saveFiles',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.result === 'success') {
                    alert('Files uploaded');
                    setTimeout(function() {
                        window.location.href = '/file/showFiles';
                    }, 1000);
                } else {
                    alert(response.message);
                }
            },
        });
        return false;
    });


</script>