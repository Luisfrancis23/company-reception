document.addEventListener("DOMContentLoaded", function () {

    const canvas = document.getElementById("signature-pad");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    const clearBtn = document.getElementById("clearSignature");
    const saveBtn = document.getElementById("saveSignature");
    const hiddenInput = document.getElementById("signatureData");
    const statusText = document.getElementById("signatureStatus");
    const visitorForm = document.getElementById("visitorForm");

    let drawing = false;

    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        ctx.scale(2, 2);
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.strokeStyle = "#0d2b4e";
    }
    resizeCanvas();

    window.addEventListener("resize", resizeCanvas);

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDraw(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function stopDraw(e) {
        drawing = false;
        ctx.beginPath();
    }

    canvas.addEventListener("mousedown", startDraw);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stopDraw);
    canvas.addEventListener("mouseleave", stopDraw);

    canvas.addEventListener("touchstart", startDraw, { passive: false });
    canvas.addEventListener("touchmove", draw, { passive: false });
    canvas.addEventListener("touchend", stopDraw);

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (hiddenInput) hiddenInput.value = "";
            if (statusText) {
                statusText.textContent = "Signature cleared.";
                statusText.className = "small ms-2 text-danger";
            }
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener("click", function () {
            if (isCanvasBlank()) {
                if (statusText) {
                    statusText.textContent = "Please draw a signature first.";
                    statusText.className = "small ms-2 text-danger";
                }
                return;
            }
            const dataUrl = canvas.toDataURL("image/png");
            if (hiddenInput) hiddenInput.value = dataUrl;
            if (statusText) {
                statusText.textContent = "Signature saved!";
                statusText.className = "small ms-2 text-success";
            }
        });
    }

    function isCanvasBlank() {
        const blank = document.createElement("canvas");
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    }

    if (visitorForm) {
        visitorForm.addEventListener("submit", function (e) {
            if (hiddenInput && hiddenInput.value === "" && !isCanvasBlank()) {
                hiddenInput.value = canvas.toDataURL("image/png");
            }
        });
    }
});
