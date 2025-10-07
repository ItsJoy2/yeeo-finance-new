@extends('admin.layouts.app')
@section('content')
<div class="p-5">
    <h4>Create New Plan</h4>
    <form method="POST" action="{{ route('admin.plans.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.pages.plan.form')
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const returnTypeSelect = document.getElementById('returnTypeSelect');
        const durationLabel = document.getElementById('durationLabel');

        function updateDurationLabel() {
            const selectedValue = returnTypeSelect.value;
            if (selectedValue === 'monthly') {
                durationLabel.textContent = 'Duration (in months)';
            } else if (selectedValue === 'daily') {
                durationLabel.textContent = 'Duration (in days)';
            } else {
                durationLabel.textContent = 'Duration';
            }
        }

        // Initial label update (in case of edit form)
        updateDurationLabel();

        // Update on change
        returnTypeSelect.addEventListener('change', updateDurationLabel);
    });
</script>
