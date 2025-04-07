$(function(){
    new DataTable('#myTable', {
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'copyHtml5', className: 'text-bg-dark',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'text-bg-dark',
                    },
                    {
                        extend: 'csvHtml5', className: 'text-bg-dark',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'text-bg-dark',
                        orientation: 'landscape', // Set orientation to landscape
                        pageSize: 'A4', // Set page size to A4
                        customize: function(doc) {
                            // Check if the content array and table object exist
                            if (doc.content && doc.content[1] && doc.content[1].table) {
                                // Adjust layout
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                // Add margins
                                doc.pageMargins = [4, 4, 4, 4]; // Top, left, bottom, right
                                // Adjust font size
                                doc.defaultStyle.fontSize = 8; // Set font size
                                // Add alignment
                                doc.content[1].table.body.forEach(function(row) {
                                    row.forEach(function(cell) {
                                        cell.alignment = 'center';
                                    });
                                });
                            }
                        }
                    }
                ],
            }
        },
        scrollX: true,
        responsive: true
    });
    $('input[type=search]').addClass(`bg-light mb-2`);
    
    // scrollX: true,
    //     responsive: true,
    // $('#myTable').addClass('badge');
    
    // $('#myTable').removeClass('no-footer');

    $('#course').on('change',function(){
        var selectedValue = $(this).val();
        // Find the selected option
        var duration = $('option[value="' + selectedValue + '"]').attr('duration');
        $('#batch').html(batch(duration));
    });

    $('#op_course').on('change',function(){
        var selectedValue = $(this).val();
        // Find the selected option
        var duration = $('option[value="' + selectedValue + '"]').attr('duration');
        $('#op_batch').html(batch(duration));
    });

    function randomBgColor() {
        // Generate random RGB values
        var red = Math.floor(Math.random() * 256);
        var green = Math.floor(Math.random() * 256);
        var blue = Math.floor(Math.random() * 256);
    
        // Format the RGB values into a CSS color string
        var color = "rgb(" + red + ", " + green + ", " + blue + ")";
    
        return color;
    }

    window.randomBgColor = randomBgColor;
});


function batch(duration) {
    var year = new Date().getFullYear();
    if (duration == null) {
        return;
    } else {
        var batch = [];
        batch.push(`<option value="">Select Batch</option>`);
        for (var i = 3; i >= 0; i--) {
            batch.push(`<option value="${(year - i) + '-' + (year - i + parseInt(duration))}">${(year - i) + '-' + (year - i + parseInt(duration))}</option>`);
        }
        return batch;
    }
}

function oldinputvalcheck(oldvalue,storevalue){
    return oldvalue != '' ? oldvalue : storevalue;
}

window.batch = batch;
window.oldinputvalcheck = oldinputvalcheck;