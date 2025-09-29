let Utils = {
    /*
    - We have jQuery plugin ~ DataTables
    - With this we created a function for automatic rendering of tables 
    - `table_id` ~ ID of our table 
    - `columns` ~ fields that will be shown 
    - `data` ~ data that we get from our APIs
    - `pageLength` ~ how many rows in one page (default = 15)
    */
    datatable: function (table_id, columns, data, pageLength = 15) {
        // if table already exists, destroy it so it doesn't duplicate
        if($.fn.dataTable.isDataTable("#" + table_id)) $("#" + table_id).DataTable().destroy();

        // initializes new DataTable with data, columns and pageLength
        $("#" + table_id).DataTable({
            data: data,
            columns: columns,
            pageLength: pageLength,
            lengthMenu: [2, 5, 10, 15, 25, 50, 100, "All"],
        });
    },

    // decodes our JWT token (header.payload.signature)
    parseJwt: function(token) {
        if (!token) return null;

        try {
            const payload = token.split(".")[1];
            const decoded = atob(payload);
            return JSON.parse(decoded);
        } catch (e) {
            console.error("Invalid JWT Token, " , e);
            return null;
        }
    }
}