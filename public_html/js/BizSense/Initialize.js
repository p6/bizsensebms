/**
 * The tables with class data_table requires zebra cross coloring
 */
dojo.addOnLoad(function(){
    dojo.query(".data_table tbody tr:nth-child(odd)").addClass("odd");
    dojo.query(".data_table tbody tr:nth-child(even)").addClass("even");
});
