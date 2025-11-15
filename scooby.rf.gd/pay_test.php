<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
</head>
<body>
    <h1>UPI Payment</h1>
    <form id="upiForm">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <button type="button" onclick="launchUPIPayment()">Pay Now</button>
    </form>

    <script>
        function launchUPIPayment() {
            const upiID = "scoobytv@upi";
            const amount = 49;
            const email = document.getElementById("email").value;
            const remarks = encodeURIComponent(email);

            const upiLink = `upi://pay?pa=${upiID}&pn=ScoobyTV&am=${amount}&cu=INR&tn=${remarks}`;

            window.location.href = upiLink;
            
        }
    </script>
</body>
</html>
