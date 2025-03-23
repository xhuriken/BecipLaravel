document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const companySelect = document.getElementById('company_id');
    const companyField = companySelect?.closest('.mb-2');

    if(!roleSelect){return}
    function toggleCompanyField() {
        if (!roleSelect || !companySelect || !companyField) return;

        const selectedRole = roleSelect.value;

        if (selectedRole === 'client') {
            companyField.style.display = '';
            companySelect.value = ''; // Aucune
        } else {
            companyField.style.display = 'none';
            // Sélectionne BECIP
            const becipOption = Array.from(companySelect.options).find(opt =>
                opt.text.toLowerCase().includes('becip') //To lower case assure si jamais c'est Becip, ou BECIP, ou beCIP...
            );
            if (becipOption) {
                companySelect.value = becipOption.value;
            }
        }
    }


    toggleCompanyField();
    roleSelect.addEventListener('change', toggleCompanyField);
});
