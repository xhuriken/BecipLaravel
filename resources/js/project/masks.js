document.addEventListener('DOMContentLoaded', function () {
    const projectContainer = document.getElementById('project-container');
    // for dodge errors
    if (!projectContainer) return;

    const projectId = projectContainer.getAttribute('data-project-id');
    //first checkbox
    const maskValidedCheckbox = document.querySelector('input[data-label="mask-valid"]');
    //second checkbox
    const maskDistributedCheckbox = document.querySelector('input[data-label="mask-distrib"]');

    function updateMaskValidated() {
        fetch(window.maskValidedRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({
                project_id: projectId,
                is_mask_valided: maskValidedCheckbox.checked ? 1 : 0
            })
        }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Mask validated updated");
                }
            }).catch(error => console.error("Error updating mask validated:", error));
    }

    function updateMaskDistributed() {
        const isMaskOn = maskDistributedCheckbox.checked;

        fetch(window.maskDistributedRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({
                project_id: projectId,
                is_mask_distributed: isMaskOn ? 1 : 0
            })
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                location.reload();
            //     console.log(`✅ Masque distribution ${isMaskOn ? 'activé' : 'désactivé'}`);
            //
            //     const table = document.getElementById('files-table');
            //     const dataTable = $('#files-table').DataTable();
            //
            //     const allHeadRows = [
            //         ...table.querySelectorAll('thead tr'),
            //         ...document.querySelectorAll('.dt-scroll-head thead tr')
            //     ];
            //     const bodyRows = table.querySelectorAll('tbody tr');
            //
            //     const headerHTML = `
            // <th data-label="Distribuer" data-orderable="false">
            //     <i class="fa-solid fa-print"></i>
            // </th>
            // <th data-label="Impressions" data-orderable="false">
            //     <i class="fa-solid fa-sheet-plastic"></i>
            // </th>`;
            //
            //     const getDistribCells = row => {
            //         const fileId = row.getAttribute('data-id');
            //         const isValidated = row.querySelector('.is-validated-checkbox')?.checked;
            //         const disabled = !isValidated || row.querySelector('.distribution-checkbox')?.disabled ? 'disabled' : '';
            //         const count = row.querySelector('td[data-label="Impressions"]')?.textContent.trim() || '0';
            //
            //         return [
            //             `<td data-label="Distribuer">
            //         <input type="checkbox" name="print_files[]" value="${fileId}" class="distribution-checkbox" ${disabled}>
            //     </td>`,
            //             `<td data-label="Impressions">${count}</td>`
            //         ];
            //     };
            //
            //     if (isMaskOn) {
            //         // ❌ Supprimer les colonnes
            //         [...allHeadRows, ...bodyRows].forEach(tr => {
            //             tr.lastElementChild?.remove();
            //             tr.lastElementChild?.remove();
            //         });
            //         console.log("🧽 Colonnes supprimées !");
            //     } else {
            //         // ✅ Ajouter les colonnes
            //         allHeadRows.forEach(tr => tr.insertAdjacentHTML('beforeend', headerHTML));
            //         bodyRows.forEach(tr => {
            //             const [col1, col2] = getDistribCells(tr);
            //             tr.insertAdjacentHTML('beforeend', col1 + col2);
            //         });
            //         console.log("➕ Colonnes réintégrées !");
            //     }
            //
            //     // ✅ Redessiner DataTable
            //     dataTable.columns.adjust().draw(false);
            //
            //     // ✅ Repaint du scroll-head
            //     const scrollHead = document.querySelector('.dt-scroll-head');
            //     scrollHead?.classList.toggle('repaint-fix');
            //     scrollHead?.offsetHeight;
            //     scrollHead?.classList.toggle('repaint-fix');
            //
            //     // ✅ Corriger largeur fantôme dans scroll-body
            //     const scrollBodyWrapper = document.querySelector('.dt-scroll-body');
            //     const scrollBodyTable = scrollBodyWrapper?.querySelector('table');
            //     if (scrollBodyTable) {
            //         scrollBodyTable.style.width = '100%';
            //         scrollBodyWrapper.scrollLeft = 0;
            //     }
            })
            .catch(err => {
                console.error("❌ Erreur update mask distributed:", err);
            });
    }



    //update when click
    if (maskValidedCheckbox) maskValidedCheckbox.addEventListener('change', updateMaskValidated);
    if (maskDistributedCheckbox) maskDistributedCheckbox.addEventListener('change', updateMaskDistributed);
});
