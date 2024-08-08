/**
 * JavaScript pour gérer les actions bulk
 */
jQuery(document).ready(function($) {

    $("#doaction, #doaction2").after('<div id="imm_progress_wrapper" style="width: 100%;"><progress id="imm_progress" max="100" value="0" style="width: 100%; display: none;"></progress><span id="imm_progress_text" style="display: none; margin-left: 10px;"></span></div>');

    $("#doaction, #doaction2").click(function(e) {
        var action = $(this).prev("select").val();
        if (action === "generate_attributes_file" || action === "generate_attributes_gpt-4o" || action === "generate_attributes_gpt-4o-mini") {
            e.preventDefault();
            var post_ids = [];
            $("tbody th.check-column input[type='checkbox']:checked").each(function() {
                post_ids.push($(this).val());
            });

            if (post_ids.length === 0) return;

            var progressBar = $("#imm_progress");
            var progressText = $("#imm_progress_text");
            progressBar.show();
            progressText.show();
            progressBar.attr("max", post_ids.length);
            progressBar.val(0);
            progressText.text("0/" + post_ids.length);

            function processBulkAction(index) {
                if (index >= post_ids.length) {
                    progressBar.hide();
                    progressText.hide();
                    window.location.reload();
                    return;
                }

                $.ajax({
                    url: imm_bulk_params.ajax_url,
                    type: "POST",
                    data: {
                        action: "imm_handle_generate_bulk_action",
                        action_type: action,
                        post_ids: [post_ids[index]],
                        nonce: imm_bulk_params.nonce
                    },
                    success: function(response) {
                        progressBar.val(index + 1);
                        progressText.text((index + 1) + "/" + post_ids.length);
                        processBulkAction(index + 1);
                    }
                });
            }

            processBulkAction(0);
        }
    });
});