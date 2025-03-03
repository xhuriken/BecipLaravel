document.addEventListener('DOMContentLoaded', function() {
    const distributionCheckboxes = document.querySelectorAll('.distribution-checkbox');

    distributionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr'); // get the row file
            const countCell = row.querySelector('[data-label="Impressions"]'); // get the cell of this row
            let currentCount = parseInt(countCell.textContent) || 0;

            if (this.checked) {
                countCell.textContent = currentCount + 1; // add +1
            }
            else {
                countCell.textContent = Math.max(currentCount - 1, 0);
            }
        });
    });

    document.querySelectorAll('.distribution-checkbox').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const countCell = row.querySelector('[data-label="Impressions"]');
        const count = parseInt(countCell.textContent) || 0;

        if (count >= 1) {
            checkbox.disabled = true;
        }
    });
});
