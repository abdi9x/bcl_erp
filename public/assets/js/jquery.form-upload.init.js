$(function () {
    $(".dropify_ktp").dropify({
        messages: {
            'default': 'Upload Foto Identitas',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong happended.'
        }
    });
    $('.dropify_foto').dropify({
        messages: {
            'default': 'Upload Foto kelihatan wajah',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong happended.'
        }
    });
    $('.dropify_lain').dropify({
        messages: {
            'default': 'Upload dokumen pendukung lainnya',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong happended.'
        }
    });
    var e = $("#input-file-events").dropify();
    e.on("dropify.beforeClear", function (e, r) {
        return confirm('Do you really want to delete "' + r.file.name + '" ?')
    }), e.on("dropify.afterClear", function (e, r) {
        alert("File deleted")
    }), e.on("dropify.errors", function (e, r) {
        console.log("Has Errors")
    });
    var r = $("#input-file-to-destroy").dropify();
    r = r.data("dropify"), $("#toggleDropify").on("click", function (e) {
        e.preventDefault(), r.isDropified() ? r.destroy() : r.init()
    })
});