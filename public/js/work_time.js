//window.onload = load_work_time();

var sheet;
function load_work_time() {
    var dimensions = [7, 48];

    var dayList = [
        { name: "一" }, { name: "二" }, { name: "三" }, { name: "四" }, { name: "五" },
        { name: "六" }, { name: "日" }

    ];

    var hourList = [
        { name: "00", title: "00:00-00:30" }, { name: "01", title: "00:30-01:00" }, { name: "02", title: "01:00-01:30" }, { name: "03", title: "01:30-02:00" }, { name: "04", title: "02:00-02:30" },
        { name: "05", title: "02:30-03:00" }, { name: "06", title: "03:00-03:30" }, { name: "07", title: "03:30-04:00" }, { name: "08", title: "04:00-04:30" }, { name: "09", title: "04:30-05:00" },
        { name: "10", title: "05:00-05:30" }, { name: "11", title: "05:30-06:00" }, { name: "12", title: "06:00-06:30" }, { name: "13", title: "06:30-07:00" }, { name: "14", title: "07:00-07:30" },
        { name: "15", title: "12:00-13:00" }, { name: "16", title: "13:00-13:30" }, { name: "17", title: "13:00-14:00" }, { name: "18", title: "14:00-15:00" }, { name: "19", title: "15:00-16:00" },
        { name: "20", title: "16:00-17:00" }, { name: "21", title: "17:00-17:30" }, { name: "22", title: "17:00-18:00" }, { name: "23", title: "18:00-19:00" }, { name: "24", title: "19:00-20:00" },
        { name: "25", title: "20:00-21:00" }, { name: "26", title: "21:00-21:30" }, { name: "27", title: "21:00-22:00" }, { name: "28", title: "22:00-23:00" }, { name: "29", title: "23:00-00:00" },
        { name: "30", title: "24:00-25:00" }, { name: "31", title: "00:00-01:30" }, { name: "32", title: "01:00-02:00" }, { name: "33", title: "02:00-03:00" }, { name: "34", title: "03:00-04:00" },
        { name: "35", title: "04:00-05:00" }, { name: "35", title: "00:00-01:30" }, { name: "37", title: "05:00-06:00" }, { name: "38", title: "06:00-07:00" }, { name: "39", title: "07:00-08:00" },
        { name: "40", title: "08:00-09:00" }, { name: "41", title: "00:00-01:30" }, { name: "42", title: "09:00-10:00" }, { name: "43", title: "10:00-11:00" }, { name: "44", title: "11:00-12:00" },
        { name: "45", title: "12:00-13:00" }, { name: "46", title: "00:00-01:30" }, { name: "47", title: "13:00-14:00" }, { name: "48", title: "14:00-15:00" }, { name: "49", title: "15:00-16:00" },
        { name: "50", title: "16:00-17:00" }, { name: "51", title: "00:00-01:30" }, { name: "52", title: "17:00-18:00" }, { name: "53", title: "18:00-19:00" }, { name: "54", title: "19:00-20:00" },
        { name: "55", title: "20:00-21:00" }, { name: "56", title: "00:00-01:30" }, { name: "57", title: "21:00-22:00" }, { name: "58", title: "22:00-23:00" }, { name: "59", title: "23:00-00:00" }
    ];

    var sheetData = $('#worktime').val()

    if(!sheetData){
        sheetData = [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

        ]
    }
    else{
        sheetData = JSON.parse(sheetData)
    }

    var updateRemark = function (sheet) {

        var sheetStates = sheet.getSheetStates();
        var rowsCount = dimensions[0];
        var colsCount = dimensions[1];
        var rowRemark = [];
        var rowRemarkLen = 0;
        var remarkHTML = '';

        for (var row = 0, rowStates = []; row < rowsCount; ++row) {
            rowRemark = [];
            rowStates = sheetStates[row];
            for (var col = 0; col < colsCount; ++col) {
                if (rowStates[col] === 0 && rowStates[col - 1] === 1) {
                    rowRemark[rowRemarkLen - 1] += (col <= 10 ? '0' : '') + col + ':00';
                } else if (rowStates[col] === 1 && (rowStates[col - 1] === 0 || rowStates[col - 1] === undefined)) {
                    rowRemarkLen = rowRemark.push((col <= 10 ? '0' : '') + col + ':00-');
                }
                if (rowStates[col] === 1 && col === colsCount - 1) {
                    rowRemark[rowRemarkLen - 1] += '00:00';
                }
            }
            remarkHTML = rowRemark.join("，");
            sheet.setRemark(row, remarkHTML === '' ? sheet.getDefaultRemark() : remarkHTML);
        }
    };

    sheet = $("#J_timedSheet").TimeSheet({
        data: {
            dimensions: dimensions,
            colHead: hourList,
            rowHead: dayList,
            sheetHead: { name: "周\\时" },
            sheetData: sheetData
        },
        remarks: false,
        end: function (ev, selectedArea) {
            updateRemark(sheet);
        }
    });

    updateRemark(sheet);
}

$(document).on('click', '[type="submit"]', function(){
    var sheetStates = sheet.getSheetStates();
    $('#worktime').val(JSON.stringify(sheetStates))
})
