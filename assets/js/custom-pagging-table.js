    $(document).ready(function() {
    const rowsPerPage = 4; 
    let currentPage = 1;
    const $table = $("#table_sample");
    const $tbody = $table.find("tbody");
    const $allRows = $tbody.find("tr");
    const totalRows = $allRows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);

    // --- PAGINATION SYSTEM ---
    function renderPage(page) {
        currentPage = page;
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Hide all rows initially, then show only the specific slice for this page
        $allRows.hide().slice(start, end).show();

        updatePaginationControls();
        updateCheckAllState();
    }

    function buildPaginationControls() {
        const $nav = $("#pagination-nav");
        $nav.empty();

        if (totalPages <= 1) return;

        // Previous Button
        $nav.append(`<button class="pagination-button" id="prev-btn">&laquo; Prev</button>`);

        // Page Number Buttons
        for (let i = 1; i <= totalPages; i++) {
            $nav.append(`<button class="pagination-button page-num-btn" data-page="${i}">${i}</button>`);
        }

        // Next Button
        $nav.append(`<button class="pagination-button" id="next-btn">Next &raquo;</button>`);

        renderPage(1);
    }

    function updatePaginationControls() {
        // Handle active class updates for page button states
        $(".page-num-btn").removeClass("active");
        $(`.page-num-btn[data-page="${currentPage}"]`).addClass("active");

        // Toggle disabled states for boundaries
        $("#prev-btn").prop("disabled", currentPage === 1);
        $("#next-btn").prop("disabled", currentPage === totalPages);
    }

    // Pagination Event Handlers
    $(document).on("click", ".page-num-btn", function() {
        renderPage($(this).data("page"));
    });

    $(document).on("click", "#prev-btn", function() {
        if (currentPage > 1) renderPage(currentPage - 1);
    });

    $(document).on("click", "#next-btn", function() {
        if (currentPage < totalPages) renderPage(currentPage + 1);
    });


    // --- ROW CHECKING SYSTEM ---
    
    // Master checkbox logic (Selects or deselects visible page rows)
    $("#check-all").on("change", function() {
        
        const isChecked = $(this).is(":checked");
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Only manipulate rows assigned to the currently active page view
        $allRows.slice(start, end).each(function() {
            const $row = $(this);
            $row.find(".row-checkbox").prop("checked", isChecked);
            $row.toggleClass("selected", isChecked);
        });
        
        updateSelectedCount();
    });

    // Individual checkbox click logic
    $tbody.on("change", ".row-checkbox", function() {
        const $checkbox = $(this);
        const $row = $checkbox.closest("tr");
        
        $row.toggleClass("selected", $checkbox.is(":checked"));
        
        updateCheckAllState();
        updateSelectedCount();
    });

    // Evaluates if the top "Check All" box should be active based on current page items
    function updateCheckAllState() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const $visiblePageRows = $allRows.slice(start, end);
        
        const allChecked = $visiblePageRows.length === $visiblePageRows.filter(function() {
            return $(this).find(".row-checkbox").is(":checked");
        }).length;

        $("#check-all").prop("checked", allChecked);
    }

    // Counts globally checked elements across all pages
    function updateSelectedCount() {
        const totalChecked = $allRows.filter(function() {
            return $(this).find(".row-checkbox").is(":checked");
        }).length;
        
        $("#selected-count").text(totalChecked);
    }

    // Initialize application logic
    buildPaginationControls();
});
