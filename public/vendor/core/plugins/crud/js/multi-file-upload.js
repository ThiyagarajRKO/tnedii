"use strict";
var list_photo_gallery = $('.list-photos-gallery');
var delete_file_modal = $('.delete-file-item');
let multiFileUploadUtils = {
    init:function(){
        this.bindEvents();
        this.initMultiFileUpoad();
        this.initSortable();
    },
    bindEvents:function(){
        $('.reset-gallery').on('click', function (event) { 
            event.preventDefault();      
            let target = event.target;
            let parentEl = $(target).parents('.multi-file-upload-container');
            parentEl.find('.list-photos-gallery .photo-gallery-item').remove();
            multiFileUploadUtils.updateItems(parentEl);
            $(this).addClass('hidden');
        });

        $(document).on('click', '.close-icon', function (event) {
            event.preventDefault();
            var id = $(this).parents('.photo-gallery-item').data('id');
            let parentEl = $(this).parents('.multi-file-upload-container');
            parentEl.next(delete_file_modal).find('#delete-gallery-item').data('id', id);
            parentEl.next(delete_file_modal).modal('show');
        });

        $(document).on('click', '#delete-gallery-item', function (event) {
            event.preventDefault();
            let target = event.target;
            let parentEl = $(target).parents(delete_file_modal).prev('.multi-file-upload-container');
            parentEl.next(delete_file_modal).modal('hide');
            parentEl.find('.photo-gallery-item[data-id=' + $(this).data('id') + ']').remove();
            multiFileUploadUtils.updateItems(parentEl);
            if (parentEl.find('.photo-gallery-item').length === 0) {
                $('.reset-gallery').addClass('hidden');
            }
        });


    },
    initMultiFileUpoad:function(){
        $('.btn_select_file').rvMedia({
            onSelectFiles: function (files, $el) {
                let parentEl = $($el).parents('.multi-file-upload-container');
                var last_index = parentEl.find('.list-photos-gallery .photo-gallery-item:last-child').data('id') + 1;
                last_index = (last_index) ? last_index : 0;
                $.each(files, function (index, file) {
                    let fileDetails = {
                        file:file.basename,
                        ulr:file.url,
                        size:file.size
                    }
                    let fileTemplate = CustomScript.getFileTemplate(fileDetails);
                    parentEl.find('.list-photos-gallery .row').append('<div class="col-sm-4 photo-gallery-item mb-2" data-id="' + (last_index + index) + '" data-file="' + file.url + '">'+fileTemplate+'</div>');
                });
                multiFileUploadUtils.initSortable(parentEl);
                multiFileUploadUtils.updateItems(parentEl);
                parentEl.find('.reset-gallery').removeClass('hidden');
            }
        });
    },
    initSortable:function(){
        let el = document.getElementById('list-photos-items');
        //let el = document.getElementsByClassName('list-photos-items');
        Sortable.create(el, {
            group: 'galleries', // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
            sort: true, // sorting inside list
            delay: 0, // time in milliseconds to define when the sorting should start
            disabled: false, // Disables the sortable if set to true.
            store: null, // @see Store
            animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
            handle: '.photo-gallery-item',
            ghostClass: 'sortable-ghost', // Class name for the drop placeholder
            chosenClass: 'sortable-chosen', // Class name for the chosen item
            dataIdAttr: 'data-id',

            forceFallback: false, // ignore the HTML5 DnD behaviour and force the fallback to kick in
            fallbackClass: 'sortable-fallback', // Class name for the cloned DOM Element when using forceFallback
            fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body

            scroll: true, // or HTMLElement
            scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
            scrollSpeed: 10, // px

            // dragging ended
            onEnd: () => {
                multiFileUploadUtils.updateItems();
            }
        });
        
    },
    updateItems: function (targetEl) {
        let items = [];
        let el = (targetEl) ? targetEl.find('.photo-gallery-item') : $('.photo-gallery-item');
        $.each(el, (index, widget) => {
            $(widget).data('id', index);
            items.push({file: $(widget).data('file'),fileSize:$(widget).data('file-size')});
        });

        targetEl.find('input[type="hidden"]').val(JSON.stringify(items));
    }

    

    
}
$(document).ready(function () {
     $(document).find('.js-rv-media-change-filter-group').attr('disabled',false);
    multiFileUploadUtils.init();
});
