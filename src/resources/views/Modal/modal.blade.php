@if(!isset($hideHeader) || $hideHeader != true)
<div class="modal-header">
    <h4 class="modal-title">{{ isset($title) ? __($title) : '' }}</h4>

    @if(!isset($hideDismissBtn) || $hideDismissBtn != true)
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    @endif
</div>
@endif

<div class="modal-body">
    @if($result)
        {{ $resultSlot }}
    @else
        {{ $slot }}
    @endif
</div>

@if(!isset($hideCancelBtn) || (isset($hideCancelBtn) && $hideCancelBtn != true) || (isset($hideCancelBtn) && $hideCancelBtn == true && isset($buttons) && !empty($buttons)))
    <div class="modal-footer">
        @if(!isset($hideCancelBtn) || $hideCancelBtn != true)

            <button type="button" class="btn btn-default" data-dismiss="modal">{{ isset($closeBtnText) ? $closeBtnText : ($result ? 'OK' : 'Cancel') }}</button>
        @endif

        {{ !empty($buttons) ? $buttons : '' }}
    </div>
@endif

<script>

    $(function() {

        Ladda.bind('.submit-modal');

    });

    @isset($class)

        $('.modal-content').addClass('{{ $class }}');

    @endisset

</script>
