
if (jQuery().select2) {
    $(document).find(".select-multiple").select2({
        width: "100%",
        allowClear: true,
    });
    $(document).find(".select-search-full").select2({
        width: "100%",
    });
    
    $(document).find(".select-full").select2({
        width: "100%",
    });
}