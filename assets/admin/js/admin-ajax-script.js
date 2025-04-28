(function (window, document, $, undefined) {
  "use strict";

  $(window).on("load", function () {
    $("#sizepsgf-size-chart-submit")
      .val("Save All Data")
      .prop("disabled", true);
  });

  $(".sizepsgf-tab-content-settings form").on(
    "click",
    "input, select, textarea",
    function () {
      $("#sizepsgf-size-chart-submit")
        .val("Save Changes")
        .prop("disabled", false);
    }
  );

  $(document).ready(function ($) {
    $(".sizepsgf-border-size").on("input", function () {
      if ($(this).val().trim() !== "") {
        $(".sizepsgf-size-chart-color-save").prop("disabled", false);
      } else {
        $(".sizepsgf-size-chart-color-save").prop("disabled", true);
      }
    });
  });

  /* Select option to Select2 */
  function sizepsgf_option_select2() {
    var select2_args = {
      placeholder: sizepsgf_localize_obj.select_placeholder,
      allowClear: true,
    };
    $(".cmfw-select-product").each(function (index) {
      if ($(this).next(".select2-container").length) {
        $(this).next(".select2-container").replaceWith("");
      }
      $(this).select2(select2_args);
    });
  }

  sizepsgf_option_select2();

  $(document).ready(function ($) {
    $("#sizepsgf_grouped_attributes").select2({
      placeholder: "Select an attribute",
      width: "100%",
    });
  });

  $(document).ready(function ($) {
    $(".cmfw-select-product-tags").select2({
      placeholder: "Select Tags",
      width: "100%",
    });
  });

  $(document).ready(function ($) {
    $(".cmfw-select-product-sizechart").select2({
      placeholder: "Select Size Post",
      width: "100%",
    });
  });

  /* Add color picker when document is ready */
  $(".sizepsgf-gen-item-con .cmfw-section-bg").wpColorPicker({
    change: function (event, ui) {
      // Enable the button when color is changed
      $(".sizepsgf-size-chart-color-save").prop("disabled", false);
    },
  });

  $(document).ready(function ($) {
    function toggleSizeChartFields() {
      let selectedValue = $(
        'select[name="sizepsgf_size_chart_position"]'
      ).val();

      if (selectedValue === "cmfw-popup-modal") {
        $(
          ".sizepsgf_size_chart_popup_position, .cmfw-tab-heading-title, .sizepsgf-tab-content-field"
        ).fadeIn();
      } else {
        $(
          ".sizepsgf_size_chart_popup_position, .cmfw-tab-heading-title, .sizepsgf-tab-content-field"
        ).fadeOut();
      }
    }

    toggleSizeChartFields();

    $('select[name="sizepsgf_size_chart_position"]').on("change", function () {
      toggleSizeChartFields();
    });
  });

  $(document).ready(function ($) {
    $(".sizepsgf-tabs a").on("click", function (e) {
      e.preventDefault();
      var tab = $(this).attr("href");
      $(".sizepsgf-tab-content").hide();
      $(tab).show();
      $(".sizepsgf-tabs a").removeClass("active");
      $(this).addClass("active");
    });

    $("#sizepsgf-product-search").on("input", function () {
      var query = $(this).val().toLowerCase();
      var resultsContainer = $("#cmfw-search-results");
      resultsContainer.empty();

      if (query.length > 2) {
        $.ajax({
          url: sizepsgf_localize_obj.ajax_url,
          method: "POST",
          data: {
            action: "sizepsgf_search_products",
            keyword: query,
            nonce: sizepsgf_localize_obj.nonce,
          },
          success: function (response) {
            resultsContainer.empty();

            if (response.success) {
              var products = response.data;
              if (products.length) {
                products.forEach(function (product) {
                  resultsContainer.append(`
                      <li>
                          <label>
                              <input type="checkbox" name="sizepsgf_products[]" value="${product.id}">
                              ${product.name}
                          </label>
                      </li>
                  `);
                });
              } else {
                resultsContainer.append(
                  '<li><?php esc_html_e("No products found.", "cmfw"); ?></li>'
                );
              }
            } else {
              resultsContainer.append(
                '<li><?php esc_html_e("Error fetching products.", "cmfw"); ?></li>'
              );
            }
          },
          error: function () {
            resultsContainer.empty();
            resultsContainer.append(
              '<li><?php esc_html_e("Error fetching products.", "cmfw"); ?></li>'
            );
          },
        });
      }
    });
  });

  // size chart settings

  $(document).ready(function ($) {
    $(".cmfw-settings-general-tab").on("submit", function (e) {
      e.preventDefault();

      var formData = $(this).serialize();
      formData += "&action=sizepsgf_size_chart_save_options";
      formData += "&nonce=" + sizepsgf_localize_obj.nonce;

      var saveButton = $("#sizepsgf-size-chart-submit");

      $.ajax({
        type: "POST",
        url: sizepsgf_localize_obj.ajax_url,
        data: formData,
        beforeSend: function () {
          saveButton.val("Saving...").prop("disabled", false);
        },
        success: function (response) {
          saveButton.val("Save All Data").prop("disabled", true);
          saveButton.attr("disabled", "disabled");
        },
        error: function (jqXHR, textStatus, errorThrown) {},
      });
    });
  });

  // color tab settings

  $(document).ready(function ($) {
    $(".cmfw-settings-color-normal-tab").on("submit", function (e) {
      e.preventDefault();

      var formData = $(this).serialize();
      formData += "&action=sizepsgf_size_chart_color_save_options";
      formData += "&nonce=" + sizepsgf_localize_obj.nonce;

      var saveButton = $("#sizepsgf-size-chart-color-save");

      $.ajax({
        type: "POST",
        url: sizepsgf_localize_obj.ajax_url,
        data: formData,
        beforeSend: function () {
          saveButton.val("Saving...").prop("disabled", false);
        },
        success: function (response) {
          saveButton.val("Save All Data").prop("disabled", true);
          saveButton.attr("disabled", "disabled");
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log("AJAX error:", textStatus, errorThrown);
          alert("AJAX error: " + textStatus);
        },
      });
    });
  });

  // advanced color settings

  $(document).ready(function ($) {
    $(".cmfw-advancedcolor-color-normal-tab").on("submit", function (e) {
      e.preventDefault();

      var formData = $(this).serialize();
      formData += "&action=sizepsgf_advancedsize_color_save_options";
      formData += "&nonce=" + sizepsgf_localize_obj.nonce;

      var saveButton = $("#sizepsgf-size-chart-advanced-color-save");

      $.ajax({
        type: "POST",
        url: sizepsgf_localize_obj.ajax_url,
        data: formData,
        beforeSend: function () {
          saveButton.val("Saving...").prop("disabled", false);
        },
        success: function (response) {
          saveButton.val("Save All Data").prop("disabled", true);
          saveButton.attr("disabled", "disabled");
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log("AJAX error:", textStatus, errorThrown);
          alert("AJAX error: " + textStatus);
        },
      });
    });
  });

  jQuery(document).ready(function ($) {
    function sizepsgfToggleSizeGuideFields() {
      var selectedValue = $("#difficulty-level").val();

      if (selectedValue === "cmfw-additional-tab") {
        $("#cmfw-input-field-settings.cmfw-tab-heading-title").remove();
        $("#cmfw-input-field-settings.sizepsgf-tab-content-field").remove();
        $(".sizepsgf_size_chart_popup_position").remove();
      } else if (selectedValue === "cmfw-popup-modal") {
        if (!$("#cmfw-input-field-settings.cmfw-tab-heading-title").length) {
          $("#difficulty-level").after(`
              <div id="cmfw-input-field-settings" class="cmfw-tab-heading-title">
                  <label for="sizepsgf_size_heading_title">Size Guide Heading</label>
                  <input type="text" id="cmfw-tab-title" value="Size Guide" placeholder="Size Guide" name="sizepsgf_size_heading_title">
              </div>
          `);
        }
        if (
          !$("#cmfw-input-field-settings.sizepsgf-tab-content-field").length
        ) {
          $("#difficulty-level").after(`
              <div id="cmfw-input-field-settings" class="sizepsgf-tab-content-field">
                  <label for="sizepsgf_size_guide_content">Size Guide Content</label>
                  <input type="text" id="cmfw-tab-title" value="This is an approximate conversion table to help you find your size." placeholder="Content" name="sizepsgf_size_guide_content">
              </div>
          `);
        }
        if (!$(".sizepsgf_size_chart_popup_position").length) {
          $("#difficulty-level").after(`
              <label for="sizepsgf_sizechart_popup_position">Popup Size Chart Position</label>
              <select id="difficulty-level" name="sizepsgf_sizechart_popup_position" class="sizepsgf_size_chart_popup_position">
                  <option value="">select</option>
                  <option value="woocommerce_after_add_to_cart_form">After - Add to cart</option>
                  <option value="woocommerce_before_add_to_cart_form">Before - Add to cart</option>
                  <option value="woocommerce_product_meta_end">After - Product Meta</option>
                  <option value="woocommerce_product_meta_start">Before - Product Meta</option>
                  <option value="woocommerce_single_product_summary">Before - Product summary</option>
                  <option value="woocommerce_after_single_product_summary">After - Product summary</option>
              </select>
          `);
        }
      }
    }

    // Initial call to set the visibility on page load
    sizepsgfToggleSizeGuideFields();

    // Add event listener for the select field
    $("#difficulty-level").change(function () {
      sizepsgfToggleSizeGuideFields();
    });
  });
})(window, document, jQuery);
