{{-- Category --}}
<div class="form-group">
    <label>Trading Pair</label>
    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
        <option value="">-- Select Pair --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id', $plan->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Plan Name --}}
<div class="form-group">
    <label>Plan Name</label>
    <input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror"
           value="{{ old('plan_name', $plan->plan_name ?? '') }}" required>
    @error('plan_name')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Image --}}
<div class="form-group">
    <label>Image</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
    @error('image')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror

    @if (!empty($plan->image))
        <img src="{{ asset('public/storage/' . $plan->image) }}" alt="Image"
             style="width:40px; height:40px; object-fit:cover; margin-top:5px;">
    @endif
</div>

{{-- Min Investment --}}
<div class="form-group">
    <label>Minimum Investment ($)</label>
    <input type="number" name="min_investment" class="form-control @error('min_investment') is-invalid @enderror"
           value="{{ old('min_investment', $plan->min_investment ?? 0) }}" required>
    @error('min_investment')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Max Investment --}}
<div class="form-group">
    <label>Maximum Investment ($)</label>
    <input type="number" name="max_investment" class="form-control @error('max_investment') is-invalid @enderror"
           value="{{ old('max_investment', $plan->max_investment ?? 0) }}" required>
    @error('max_investment')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Return Type --}}
<div class="form-group">
    <label>Return Type</label>
    <select id="returnTypeSelect" name="return_type" class="form-control @error('return_type') is-invalid @enderror" required>
        <option value="">-- Select Return Type --</option>
        <option value="daily" {{ old('return_type', $plan->return_type ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
        <option value="monthly" {{ old('return_type', $plan->return_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
    </select>
    @error('return_type')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Duration --}}
<div class="form-group">
    <label id="durationLabel">Duration (in {{ old('return_type', $plan->return_type ?? '') == 'monthly' ? 'months' : 'days' }})</label>
    <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror"
           value="{{ old('duration', $plan->duration ?? 0) }}" required>
    @error('duration')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- PNL Return --}}
<div class="form-group">
    <label>PNL Return (%)</label>
    <input type="number" step="0.01" name="pnl_return" class="form-control @error('pnl_return') is-invalid @enderror"
           value="{{ old('pnl_return', $plan->pnl_return ?? 0) }}" required>
    @error('pnl_return')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Referral Bonus --}}
<div class="form-group">
    <label>PNL Bonus (%)</label>
    <input type="number" step="0.01" name="pnl_bonus" class="form-control @error('pnl_bonus') is-invalid @enderror"
           value="{{ old('pnl_bonus', $plan->pnl_bonus ?? 0) }}">
    @error('pnl_bonus')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

{{-- Status --}}
<div class="form-group">
    <label>Status</label>
    <select name="status" class="form-control @error('status') is-invalid @enderror">
        <option value="active" {{ old('status', $plan->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $plan->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>
