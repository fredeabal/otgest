/****************************************
 *         Table Responsive             *
 ****************************************/
$(function () {
  $("#config-table").DataTable({
    responsive: true,
    language: {
      url: "assets/js/datatable/Spanish.json"
    }
  });
});

/****************************************
 *       Basic Table                   *
 ****************************************/
$("#zero_config").DataTable({
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *       Default Order Table           *
 ****************************************/
$("#default_order").DataTable({
  order: [[3, "desc"]],
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *       Multi-column Order Table      *
 ****************************************/
$("#multi_col_order").DataTable({
  columnDefs: [
    {
      targets: [0],
      orderData: [0, 1],
    },
    {
      targets: [1],
      orderData: [1, 0],
    },
    {
      targets: [4],
      orderData: [4, 0],
    },
  ],
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *       Complex header Table          *
 ****************************************/
$("#complex_header").DataTable({
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *       DOM positioning Table         *
 ****************************************/
$("#DOM_pos").DataTable({
  dom: '<"top"i>rt<"bottom"flp><"clear">',
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *     alternative pagination Table    *
 ****************************************/
$("#alt_pagination").DataTable({
  pagingType: "full_numbers",
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *     vertical scroll Table    *
 ****************************************/
$("#scroll_ver").DataTable({
  scrollY: "300px",
  scrollCollapse: true,
  paging: false,
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 * vertical scroll,dynamic height Table *
 ****************************************/
$("#scroll_ver_dynamic_hei").DataTable({
  scrollY: "50vh",
  scrollCollapse: true,
  paging: false,
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 *     horizontal scroll Table    *
 ****************************************/
$("#scroll_hor").DataTable({
  scrollX: true,
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 * vertical & horizontal scroll Table  *
 ****************************************/
$("#scroll_ver_hor").DataTable({
  scrollY: 300,
  scrollX: true,
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});

/****************************************
 * Language - Comma decimal place Table  *
 ****************************************/
$("#lang_comma_deci").DataTable({
  language: {
    url: "assets/js/datatable/Spanish.json",
    decimal: ",",
    thousands: "."
  }
});

/****************************************
 *         Language options Table      *
 ****************************************/
$("#lang_opt").DataTable({
  language: {
    url: "assets/js/datatable/Spanish.json"
  }
});
