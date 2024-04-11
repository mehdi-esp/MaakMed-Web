export function SubscriptionsFeature() {
    const planNameInput = document.getElementById('planNameInput');
    const statusSelect = document.getElementById('statusSelect');

    const fetchSubscriptions = () => {
        let planName = planNameInput.value;
        let status = statusSelect.value;

        fetch(`/subscription/search?planName=${planName}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                let tbody = document.querySelector('tbody');
                tbody.innerHTML = '';

                data.forEach(subscription => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${subscription.planName}</td>
                        <td>${subscription.patientUsername}</td>
                        <td>${subscription.startDate}</td>
                        <td>${subscription.endDate}</td>
                        <td>${subscription.status}</td>
                        <td><a class="btn primary-btn m-2" href="/subscription/UpdateSubscription/${subscription.id}">Update</a></td>
                        <td><a class="btn primary-btn m-2" href="/subscription/DeleteSubscription/${subscription.id}">Cancel</a></td>
                    </tr>
                    `;
                });
            });
    };

    planNameInput.addEventListener('input', fetchSubscriptions);
    statusSelect.addEventListener('change', fetchSubscriptions);
}