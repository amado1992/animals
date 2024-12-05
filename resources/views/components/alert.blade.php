<script>
    $(function(){
        @if(Session::has('success'))
            $.NotificationApp.send("Success message!", "{{ Session::get('success') }}", 'top-right', '#5ba035', 'success');
        @endif

        @if(Session::has('info'))
            $.NotificationApp.send("Info message!", "{{ Session::get('info') }}", 'top-right', '#3b98b5', 'info');
        @endif

        @if(Session::has('warning'))
            $.NotificationApp.send("Warning message!", "{{ Session::get('warning') }}", 'top-right', '#da8609', 'warning');
        @endif

        @if(Session::has('error'))
            $.NotificationApp.send("Error message!", "{{ Session::get('error') }}", 'top-right', '#bf441d', 'error');
        @endif
    });
</script>
