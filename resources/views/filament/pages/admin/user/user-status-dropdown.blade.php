<div>
    @dd($user);
    <select  wire:change="updateStatus('{{ ['id'] }}', $event.target.value)"
        class="border rounded px-2 py-1 text-sm"
    >
        <option value="0" @selected($user['status'] == 0)>Pending</option>
        <option value="1" @selected($user['status'] == 1)>Verified</option>
        <option value="2" @selected($user['status'] == 2)>Suspend</option>
    </select>
</div>
