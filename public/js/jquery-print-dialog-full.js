/**
 * Script to print surplus and wanted lists with full options.
 * Called in Our surplus and in Surplus
 */
$('#printOptions').on('click', function () {
   var count_selected_records = $(":checked.selector-thesurplus").length;
   var count_page_records = $('#countRecordsOnPage').val();
   $("label[for='count_selected_records']").html('(' + count_selected_records + ')');
   $("label[for='count_page_records']").html('(' + count_page_records + ')');

   $("#printOptionsDialog [name=print_document_type],[name=print_prices],[name=print_language],[name=print_pictures],[name=print_wanted],[name=print_stuffed]").prop('checked', false);
   $("#printOptionsDialog [name=print_wanted_list]").val('');
   $("#printOptionsDialog [name=print_wanted_list]").prop('disabled', true);
   $("#printOptionsDialog [name=filter_client_id]").html('');
   $("#printOptionsDialog .print_client").addClass('d-none');

   $('#printOptionsDialog').modal('show');
});


$('#printOptionsDialog input[name=print_wanted]:radio').change(function () {
   var wanted = $('#printOptionsDialog input[name=print_wanted]:checked').val();

   if (wanted == 'yes')
      $("#printOptionsDialog [name=print_wanted_list]").prop('disabled', false);
   else
      $("#printOptionsDialog [name=print_wanted_list]").prop('disabled', true);
});

$('#printOptionsDialog').on('submit', function (event) {
   event.preventDefault();
   showCoverSelectionModal()
});

const showCoverSelectionModal = () => {
    $('#printOptionsDialog').modal('hide');
    $('#selectFirstThreeAnimalsModal').modal('show');
    
    const export_option = $('#printOptionsDialog [name=export_option]:checked').val();
    const selIds        = getSelectedIds(export_option)
    const selInputs     = [...document.querySelectorAll('input.selector-thesurplus')].filter(input => {
        return selIds.indexOf(input.value) !== -1
    })
    const rows          = selInputs.map(input => {
        const rowNode = input.parentElement.parentElement.parentElement.parentElement
        const newNode = rowNode.cloneNode(true)
        const tmp     = document.createElement('DIV')
        tmp.appendChild(newNode)
        return tmp.children[0].innerHTML
    })
    const coverWrapper   = document.querySelector('#coverAnimalsWrapper')
    coverWrapper.innerHTML = rows
}

const getSelectedIds = (export_option) => {
    var ids = [];
    if (export_option === 'selection') {
        $(":checked.selector-thesurplus").each(function () {
            ids.push($(this).val());
        });
    } else if (export_option === 'page') {
        $(".selector-thesurplus").each(function () {
            ids.push($(this).val());
        });
    }
    if (ids.length === $('#countRecordsTotal').val()) {
        export_option = "all";
        ids = [];
    }
    return ids
}

const getPrintedSurplus = () => {
    const coverWrapper   = document.querySelector('#coverAnimalsWrapper')
    const coverAnimals   = [...coverWrapper.querySelectorAll('input.selector-thesurplus')]
    .map(input => {
        if (input.checked) {
            return input.value
        }
    })
    .filter(id => id !== undefined)

    if (coverAnimals.length < 3) {
        alert('Select at least 3 animals')
        return false
    }
    if (coverAnimals.length > 3) {
        alert(`You've selected more than 3 animals. The list will be printed, but only with the first 3 animals you selected.`)
    }

    var export_option    = $('#printOptionsDialog [name=export_option]:checked').val();
    const ids            = getSelectedIds(export_option)
    var document_type    = $('#printOptionsDialog [name=print_document_type]:checked').val();
    var prices           = $('#printOptionsDialog [name=print_prices]:checked').val();
    var language         = $('#printOptionsDialog [name=print_language]:checked').val();
    var pictures         = $('#printOptionsDialog [name=print_pictures]:checked').val();
    var stuffed          = $('#printOptionsDialog [name=print_stuffed]:checked').val();
    var wanted           = $('#printOptionsDialog [name=print_wanted]:checked').val();
    var wanted_list      = $('#printOptionsDialog [name=print_wanted_list]').val();
    var filter_client_id = $('#printOptionsDialog [name=filter_client_id]').val();

    if (export_option !== 'all' && ids.length === 0) {
        alert("There are not records to export.");
        return false
    }
    if (document_type === null || prices === null || language === null || pictures === null) {
        alert("The options: Document, Prices, Language and Pictures, must be marked.");
        return false
    }
    if (export_option !== 'all' && ids.length > 300) {
        alert("You cannot print more than 300 records. Please select HTML file and option 'All records'.");
        return false
    }
    
    var route = $('#printOptionsDialog').find('input[name="route"]').val();
    var printOptionsDialogButton = $("#printSurplusListButton").html();
    $("#printSurplusListButton").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>");
    $.ajax({
        type: 'GET',
        url: route,
        data: {
            export_option: export_option,
            document_type: document_type,
            prices: prices,
            language: language,
            pictures: pictures,
            wanted: wanted,
            wanted_list: wanted_list,
            items: ids,
            print_stuffed: stuffed,
            filter_client_id: filter_client_id,
            cover_animals: JSON.stringify(coverAnimals)
        },
        success: function (response) {
            if (response.success) {
                $('#selectFirstThreeAnimalsModal').modal('hide');
                $.NotificationApp.send("Success message!", "The printed list was downloaded", 'top-right', '#fff', 'success');
                var link = document.createElement('a');
                link.href = window.URL = response.url;
                link.download = response.fileName;
                link.click();
            } else
                alert(response.message);
        }, complete: function () {
            $("#printSurplusListButton").html(printOptionsDialogButton);
            document.querySelector('#coverAnimalsWrapper').innerHTML = ''
        },
    });
}