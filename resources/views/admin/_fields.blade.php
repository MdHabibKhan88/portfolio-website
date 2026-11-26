{{-- Renders fields defined in config/admin.php. Expects $fields, $model (Eloquent or Fluent), optional $options --}}
@php $options = $options ?? []; @endphp
@foreach($fields as $f)
    @php
        $type = $f['type'] ?? 'text';
        $name = $f['name'];
        $cols = ! empty($f['bilingual']) ? ['EN' => $name.'_en', 'বাংলা' => $name.'_bn'] : ['' => $name];
    @endphp
    <div class="row g-3 mb-3">
        @foreach($cols as $langLabel => $col)
            @php
                $raw = data_get($model, $col);
                $val = old($col, $raw instanceof \Carbon\CarbonInterface ? $raw->format('Y-m-d') : $raw);
                $label = t('f_'.$name).($langLabel ? ' ('.$langLabel.')' : '');
                $isFile = in_array($type, ['image', 'file', 'document'], true);
                $accept = ['image' => '.jpg,.jpeg,.png,.webp', 'file' => '.jpg,.jpeg,.png,.webp,.pdf', 'document' => '.pdf,.doc,.docx'][$type] ?? null;
            @endphp
            <div class="{{ $langLabel ? 'col-md-6' : 'col-12' }}">
                @if($type === 'boolean')
                    <input type="hidden" name="{{ $col }}" value="0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="f_{{ $col }}" name="{{ $col }}" value="1" @checked((bool) $val)>
                        <label class="form-check-label" for="f_{{ $col }}">{{ $label }}</label>
                    </div>
                @else
                    <label for="f_{{ $col }}" class="form-label">{{ $label }}@if(! empty($f['required']) && $langLabel !== 'বাংলা') <span class="text-danger">*</span>@endif</label>

                    @if($type === 'textarea')
                        <textarea id="f_{{ $col }}" name="{{ $col }}" rows="4" class="form-control @error($col) is-invalid @enderror">{{ $val }}</textarea>
                    @elseif($type === 'select')
                        <select id="f_{{ $col }}" name="{{ $col }}" class="form-select @error($col) is-invalid @enderror">
                            <option value="">&mdash;</option>
                            @foreach($options[$col] ?? [] as $k => $optLabel)
                                <option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                    @elseif($isFile)
                        @if($raw)
                            <div class="mb-2 small">
                                {{ t('a_current_file') }}:
                                @if($type === 'image' || \Illuminate\Support\Str::endsWith(strtolower($raw), ['.jpg', '.jpeg', '.png', '.webp']))
                                    <div><img src="{{ file_url($raw) }}" alt="" class="img-thumbnail mt-1" style="max-height: 140px;"></div>
                                @else
                                    <a href="{{ file_url($raw) }}" target="_blank" rel="noopener noreferrer">{{ basename($raw) }}</a>
                                @endif
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="remove_{{ $col }}" id="rm_{{ $col }}" value="1">
                                    <label class="form-check-label" for="rm_{{ $col }}">{{ t('a_remove_file') }}</label>
                                </div>
                            </div>
                        @endif
                        <input type="file" id="f_{{ $col }}" name="{{ $col }}" accept="{{ $accept }}" data-preview="p_{{ $col }}" class="form-control @error($col) is-invalid @enderror">
                        <img id="p_{{ $col }}" src="" alt="" class="img-thumbnail mt-2" style="max-height: 140px;" hidden>
                    @else
                        <input type="{{ in_array($type, ['email', 'url', 'date', 'number']) ? $type : 'text' }}" id="f_{{ $col }}" name="{{ $col }}" value="{{ $val }}"
                               @if($type === 'number') min="{{ $f['min'] ?? 0 }}" max="{{ $f['max'] ?? 100 }}" @endif
                               class="form-control @error($col) is-invalid @enderror">
                    @endif

                    @error($col)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @if(! empty($f['hint']) && ! $langLabel || (! empty($f['hint']) && $langLabel === 'EN'))<div class="form-text">{{ t($f['hint']) }}</div>@endif
                @endif
            </div>
        @endforeach
    </div>
@endforeach
