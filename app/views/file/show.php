<style>
    .tooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black; /
    }

    /* Tooltip text */
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;


        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;

        /* Fade in tooltip */
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>


<table border="1">
    <tr>
        <th></th>
        <th>Name</th>
    </tr>
    <?php
    if (is_array($data)) {
        foreach ($data as $file) {
            ?>
            <tr id="row_<?=$file['id']?>">
                <td><i class="fa fa-close" onclick="removeFile('<?=$file['id']?>','<?=$file['image']?>')"></i></td>
                <td class="tooltip" onclick="openImage('<?=$file['image']?>')"><?=$file['image']?>
                    <span class="tooltiptext"><?=$file['description']?></span>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<script>
    function removeFile(fileId, fileName) {
        if (confirm('Do you want remove file ' + fileName + ' ?')) {
            $('#row_' + fileId).addClass("disabled");
            $.ajax({
                url: '/file/removeFile',
                type: 'POST',
                data: {fileId: fileId},
                success: function(response) {
                    if (response.result === 'success') {
                        $('#row_' + fileId).remove();
                    } else {
                        alert(response.message);
                        setTimeout(function () {
                            window.location.reload()
                        }, 1000);
                    }
                }
            });
        }
    }

    function openImage(fileName) {
        popupCenter("/uploads/" + fileName, "Image", 400, 400);
    }

    function popupCenter(url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        let dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        let dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        let systemZoom = width / window.screen.availWidth;
        let left = (width - w) / 2 / systemZoom + dualScreenLeft
        let top = (height - h) / 2 / systemZoom + dualScreenTop
        let newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) newWindow.focus();
    }
</script>