<x-admin.header title="IP Seller Admin" />
<main class="content px-3 py-2">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="mb-0">{{isset($edit) ? 'Edit' : 'Add'}} Salary</h3>
        </div>
        <!-- Table Element -->
        <div class="card overflow-visible border-0 overflow-hidden">
            <div class="card-body">
                <x-form method="POST" action="{{ route('salary.update.multiple') }}">
                    <input type="hidden" name="salary_ids" value="{{ implode(',', $ids) }}">

                    <div class="row row-cols-2">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Employee</label>
                            <select name="employee_id[]" class="form-select choices" multiple>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ in_array($employee->id, $salaries->pluck('employee_id')->toArray()) ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->short_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-field 
                            type="text"
                            name="date" 
                            label="Date"
                            value="{{ \Carbon\Carbon::parse($last->date)->format('d-m-Y') }}"
                        />

                        <x-input-field 
                            type="text"
                            name="amount" 
                            label="Salary Amount"
                            value="{{ $last->amount }}"
                        />

                        <x-input-field 
                            type="text"
                            name="allowance"
                            label="Allowance"  
                            value="{{ $last->allowance }}"
                        />

                        <x-input-field 
                            type="text"
                            name="bonus" 
                            label="Bonus" 
                            value="{{ $last->bonus }}"
                        />

                        <div class="w-100">
                            <button type="submit" class="btn btn-primary">
                                Update Selected Salaries
                            </button>
                        </div>

                    </div>
                </x-form>

            </div>
        </div>
    </div>
</main>
<x-admin.footer />
<script>
    flatpickr("#date", {
        dateFormat: "d-m-Y"
    });
</script>