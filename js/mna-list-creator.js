jQuery( document ) . ready( function($){

    //Fetch and Display Initial Data

    function fetchInitialData() {
        $.ajax({
            type: 'GET',
            url: mna_list_object.ajax_url,
            data: { action: 'mna_list_creator_initial_data'},
            success: function(response){
                var data = JSON.parse(response);
                if(data && data.length > 0 ) {
                    data.forEach(function(row) {
                        addRowToTable(row);
                    })
                }
            }
        });
    }
    fetchInitialData();


    $( '#mna-list-submit' ).submit( function(){
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: mna_list_object.ajax_url,
            data: formData + '&action=list_creator_form_submit',
            success: function(response) {
                var newRow = JSON.parse(response);
                addRowToTable(newRow);
                clearForm();
            }
        });
        return false;
    });

    //Delete Row Via Ajax
    $(document).on( 'click', '.delete-row', function(){
        var rowId = $(this).data('row-id');

        $.ajax({
            type: 'POST',
            url: mna_list_object.ajax_url,
            data: 'row_id=' + rowId + '&action=delete_list_row',
            success: function(){
                $('#row-' + rowId).remove();
            }
        });
    });
    
    //Function To Add New Row to Table
    function addRowToTable(row) {
        var newRow = '<tr id="row-' + row.id + '"><td>' + row.name + '</td><td>' + row.email + '</td><td>' + row.phone + '</td><td><button class="delete-row" data-row-id="' + row.id + '">Delete</button></td></tr>';
        $('#list-display-table tbody' ).append(newRow);
    }

    //Clear Form
    function clearForm() {
        $('#name').val('');
        $('#email').val('');
        $('#phone').val('');
    }

});