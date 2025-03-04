document.addEventListener('DOMContentLoaded', () => {
    // PROJECT_NAME INPUT FOR "ADD PROJECT" MODAL
    const yearA = document.getElementById("add-project-year");
    const numberA = document.getElementById("add-project-number");
    const projectNameA = document.getElementById("add-project-name");

    function updateProjectNameA() {
        const year = yearA.value;
        const number = numberA.value;

        // dodge invalid chars
        if (year.length !== 2 || number.length !== 3 || isNaN(year) || isNaN(number)) {
            projectNameA.value = "";
            return;
        }

        projectNameA.value = `B${year}.${number}`;
    }

    function enforceNumericInput(event) {
        if (!/^\d$/.test(event.key)) {
            event.preventDefault();
        }
    }

    if (yearA && numberA) {
        yearA.addEventListener("input", updateProjectNameA);
        numberA.addEventListener("input", updateProjectNameA);
        yearA.addEventListener("keypress", enforceNumericInput);
        numberA.addEventListener("keypress", enforceNumericInput);
    }

    // SAME FOR EDIT PROJECT MODAL
    const yearE = document.getElementById("edit-project-year");
    const numberE = document.getElementById("edit-project-number");
    const projectNameE = document.getElementById("edit-project-name");

    function updateProjectNameE() {
        const year = yearE.value;
        const number = numberE.value;

        // Dodge invalid chars in input
        if (year.length !== 2 || number.length !== 3 || isNaN(year) || isNaN(number)) {
            projectNameE.value = "";
            return;
        }

        projectNameE.value = `B${year}.${number}`;
    }

    if (yearE && numberE) {
        yearE.addEventListener("input", updateProjectNameE);
        numberE.addEventListener("input", updateProjectNameE);
        yearE.addEventListener("keypress", enforceNumericInput);
        numberE.addEventListener("keypress", enforceNumericInput);
    }
});
