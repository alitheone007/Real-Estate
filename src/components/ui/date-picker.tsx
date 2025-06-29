import React from 'react';

interface DatePickerProps {
  placeholder?: string;
  onChange?: (date: string) => void;
  className?: string;
}

const DatePicker: React.FC<DatePickerProps> = ({ 
  placeholder = 'Select date', 
  onChange, 
  className = '' 
}) => {
  const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    onChange?.(event.target.value);
  };

  return (
    <input
      type="date"
      className={`flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ${className}`}
      placeholder={placeholder}
      onChange={handleChange}
    />
  );
};

export { DatePicker }; 