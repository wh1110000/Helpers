@if(auth()->check())

    {{ Html::script('vendor/jquery-eu-cookie-law-popup-master/js/jquery-eu-cookie-law-popup.js') }}

    <script>

        $(document).ready(function() {

            $(document).euCookieLawPopup().init({
                cookiePolicyUrl : '{{ route('page.show', ['privacy-policy']) }}',
                popupPosition : 'bottomleft',
                popupTitle : '',
                popupText : '{!! __('general.cookie_text') !!}',
                buttonContinueTitle : '{!! __('general.cookie_accept') !!}',
                buttonLearnmoreTitle : '{!! __('general.cookie_learn_more') !!}'
            });
        });

    </script>
@endif