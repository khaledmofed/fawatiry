@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 w-full rounded-clay border-clay-hairline bg-clay-canvas text-clay-ink shadow-sm transition placeholder:text-clay-muted-soft focus:border-clay-ink focus:outline-none focus:ring-2 focus:ring-clay-teal/20']) }}>
