<div class="wrapper-filter sidebarSearchContainer">
    <div>
        <div class="input-group">
            <input type="text" class="form-control sidebarSearch" placeholder="search">
            <span class="input-group-prepend">
                <button class="btn default" type="button">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        let mainLogo =  $('.page-logo > a > img').attr('src');
        $('.sidebarSearch').keyup(function() {
            var valThis = $(this).val();
            valThis = (valThis) ? valThis.toLowerCase() : valThis;
            $(".page-sidebar-menu li a .title, .sub-menu li a .title").each(function() {
                var text = $(this).text().toLowerCase();
                let parentEl = $(this).parents('li:first');
                (text.indexOf(valThis) != -1) ? $(parentEl).show(): $(parentEl).hide();
            });
        });

        $('body').on('click', '.sidebar-toggler', event => {
            let sidebarMenu = $('.page-sidebar-menu');
            
            setTimeout(function() {
                if ($('body').hasClass('page-sidebar-closed')) {
                    $('.sidebarSearchContainer').hide();
                    $('.page-logo > a > img').show();
                    $('.page-logo > a > img').attr('src','/storage/mask-group-1.png');
                    $('.page-logo > a > img').css('min-width', '25px');
                    $('.page-logo > a > h6').hide();                    
                    $('.page-footer').css('margin-left', '45px');
                    $('.form-actions > .form-actions-fixed-bottom').css('left', '45px');
                } else {
                    $('.sidebarSearchContainer').show();
                    $('.page-logo > a > h6').show();
                    $('.page-logo > a > img').attr('src',mainLogo);
                    $('.page-logo > a > img').css('min-width', '30px');
                    $('.page-footer').css('margin-left', '235px');
                    $('.form-actions > .form-actions-fixed-bottom').css('left', '235px');
                }
            }, 50)
        });
    })
</script>