<select {{ $attributes->merge(['class' => 'w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100']) }}>
  {{ $slot }}
</select>

