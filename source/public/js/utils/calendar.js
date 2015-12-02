define(function(require, exports){

    var setup = function(inputTime, onSelect){

        if (typeof onSelect !== 'function') {
            onSelect = function() {
                this.hide();
            }
        }

        var showTime= arguments[2] || false;//是否显示时间
        var dateFormat = arguments[3] || "%Y-%m-%d";//时间显示格式

        Calendar.setup({
            weekNumbers: true,
            inputField : inputTime,
            trigger    : inputTime,
            dateFormat : dateFormat,
            showTime   : showTime,
            minuteStep : 1,
            onSelect   : onSelect
        });
    };
    exports.setup = setup;

});