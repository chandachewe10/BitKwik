<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lenco Payment</title>
    <script src="https://pay.lenco.co/js/v1/inline.js"></script>
</head>
<body>
    
    <h3>Processing your payment...</h3>

    <script>
        function makePaymentsWithLenco() {
            LencoPay.getPaid({
                key: "{{ env('LENCO_PUBLIC_KEY') }}",
                reference: 'ref-' + Date.now(),
                email: "{{ auth()->user()->email }}",
                amount: {{ $data['total_amount'] }},
                currency: "ZMW",
                channels: ["card", "mobile-money"],
                customer: {
                    firstName: "{{ auth()->user()->name }}",
                    lastName: "",
                    phone: "",
                },

                onSuccess: function (response) {
                    
                    let formData = new FormData();
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("payment_data", JSON.stringify(response));
                    formData.append("amount", "{{ $data['total_amount'] }}");

                    fetch("{{ route('complete.subscription') }}", {
                        method: "POST",
                        body: formData,
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            alert("✅ Bitcoin withdrawal created successfully! Scan the QR code using your wallet to receive the payment. The QRCode will also be available in your transaction history and expires after 10 minutes.");

                            // If QR code is returned, show it
                            window.location.href = "/customer/mobile-to-bitcoins";
                        } else {
                            alert("⚠️ " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                        alert("❌ Something went wrong. Try again.");
                    });
                },

                onClose: function () {
                alert('Payment was not complete, please try again.');
                window.location.href = "/customer/mobile-to-bitcoins";
            },
                onConfirmationPending: function () {
                    alert('Your subscription will complete when payment is confirmed.');
                },
            });
        }

        window.addEventListener('DOMContentLoaded', makePaymentsWithLenco);
    </script>
</body>
</html>
