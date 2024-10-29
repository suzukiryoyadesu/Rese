$.datepicker._gotoToday = function (datepicker) {
    var target = $('#datepicker');
    var inst = this._getInst(target[0]);
    var date = new Date();
    this._setDate(inst, date);
    this._hideDatepicker();
    $('#date__form').submit();
}

$(function() {
    $('#datepicker').datepicker({
        dateFormat: 'yy年mm月dd日(D)',
        showOtherMonths: true,
        showButtonPanel: true,
    });
});
