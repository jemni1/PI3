document.getElementById("signature-form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    let canvas = document.getElementById("signature-pad");
    let signatureData = canvas.toDataURL("image/png"); // Convertir la signature en image Base64
    
    let input = document.createElement("input");
    input.type = "hidden";
    input.name = "signature";
    input.value = signatureData;
    
    this.appendChild(input);
    this.submit();
});
