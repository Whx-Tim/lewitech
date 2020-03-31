
<script>
    var dateTimeOption = {
        format: 'Y-m-d H:i:s'
    };
    $.datetimepicker.setLocale('zh');

    $('#start_time').datetimepicker(dateTimeOption);
    $('#end_time').datetimepicker(dateTimeOption);
    $('#end_at').datetimepicker(dateTimeOption);
</script>