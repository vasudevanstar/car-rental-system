/**
 * FastRide Invoice Generation Module
 * Handles the creation of branded PDF receipts using jsPDF
 */
window.FastRideInvoice = {
    generate: async (rentalData) => {
        try {
            console.log('FastRideInvoice: Generating Image for', rentalData);
            
            const b = rentalData;
            const container = document.getElementById('invoice-template');
            if (!container) throw new Error('Invoice template element not found.');

            // Fill template with real data
            document.getElementById('tmpl-id').textContent = b.id;
            document.getElementById('tmpl-email').textContent = b.customer_email || 'Verified Customer';
            document.getElementById('tmpl-date').textContent = new Date().toLocaleDateString();
            document.getElementById('tmpl-vehicle').textContent = `${b.vehicle?.brand || ''} ${b.vehicle?.name || 'Vehicle #' + b.vehicle_id}`;
            document.getElementById('tmpl-period').textContent = `${b.start_date} to ${b.end_date}`;
            document.getElementById('tmpl-amount').textContent = `$${Number(b.total_amount).toFixed(2)}`;
            document.getElementById('tmpl-total').textContent = `$${Number(b.total_amount).toFixed(2)}`;

            // Wait a tiny bit for any styles to settle
            await new Promise(r => setTimeout(r, 100));

            if (typeof html2canvas === 'undefined') {
                throw new Error('Image library (html2canvas) not loaded. Please refresh.');
            }

            const canvas = await html2canvas(container, {
                scale: 2, // High resolution
                useCORS: true,
                backgroundColor: '#ffffff'
            });

            const link = document.createElement('a');
            link.style.display = 'none';
            link.download = `FastRide_Invoice_${b.id}.png`;
            link.href = canvas.toDataURL('image/png');
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            return true;
        } catch (error) {
            console.error('Invoice Generation Error:', error);
            alert('Failed to generate Image: ' + error.message);
            return false;
        }
    }
};
