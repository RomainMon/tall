function generatePDF() {
    // Sélection de l'élément à mettre au format PDF
    const element = document.getElementById("myChart");

    // Paramètrage du PDF
    var opt = {
        margin:       1,
        filename:     'Votre graphique.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2},
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait',  text:(20, 20, 'Hello world!')}
      };



    // export au format PDf
    html2pdf().set(opt).from(element).save();

}

