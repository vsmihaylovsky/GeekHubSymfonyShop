/**
 * Created by vad on 3/14/16.
 */

$('input[name="select-all"]').click(function () {
    $('input.selected').prop('checked', this.checked);
});