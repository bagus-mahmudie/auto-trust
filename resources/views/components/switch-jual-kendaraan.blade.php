@props(['active' => 'mobil'])

<div class="mb-0">
  <label class="block text-sm font-medium text-slate-700 mb-2">
    Kendaraan apa yang ingin Anda jual?
  </label>

  <select
    id="vehicleTypeSelect"
    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-sky-200"
  >
    <option value="mobil" {{ $active === 'mobil' ? 'selected' : '' }}>Mobil</option>
    <option value="motor" {{ $active === 'motor' ? 'selected' : '' }}>Motor</option>
  </select>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const select = document.getElementById("vehicleTypeSelect");
    if (!select) return;

    select.addEventListener("change", () => {
      const val = select.value;

      if (val === "mobil") {
        window.location.href = "{{ route('mobil.create') }}";
      } else {
        window.location.href = "{{ route('motor.create') }}";
      }
    });
  });
</script>
