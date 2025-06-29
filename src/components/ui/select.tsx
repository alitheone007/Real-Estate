import React, { useState, useRef, useEffect } from 'react';

interface SelectProps {
  value?: string;
  onValueChange?: (value: string) => void;
  children: React.ReactNode;
  placeholder?: string;
  className?: string;
}

interface SelectTriggerProps {
  children: React.ReactNode;
  className?: string;
}

interface SelectContentProps {
  children: React.ReactNode;
  className?: string;
}

interface SelectItemProps {
  value: string;
  children: React.ReactNode;
  className?: string;
  onClick?: () => void;
}

interface SelectValueProps {
  placeholder?: string;
  children?: React.ReactNode;
}

const Select: React.FC<SelectProps> = ({ 
  value, 
  onValueChange, 
  children, 
  placeholder,
  className = '' 
}) => {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedValue, setSelectedValue] = useState(value || '');
  const [selectedLabel, setSelectedLabel] = useState('');
  const selectRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (selectRef.current && !selectRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSelect = (value: string, label: string) => {
    setSelectedValue(value);
    setSelectedLabel(label);
    setIsOpen(false);
    onValueChange?.(value);
  };

  return (
    <div ref={selectRef} className={`relative ${className}`}>
      <div
        className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        onClick={() => setIsOpen(!isOpen)}
      >
        <span className={selectedValue ? 'text-foreground' : 'text-muted-foreground'}>
          {selectedLabel || placeholder}
        </span>
        <svg className="h-4 w-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
        </svg>
      </div>
      
      {isOpen && (
        <div className="absolute top-full left-0 z-50 w-full mt-1 bg-background border border-input rounded-md shadow-lg">
          <div className="p-1">
            {React.Children.map(children, (child) => {
              if (React.isValidElement(child) && child.type === SelectItem) {
                return (
                  <div
                    key={child.props.value}
                    className={`relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground ${child.props.value === selectedValue ? 'bg-accent text-accent-foreground' : ''}`}
                    onClick={() => handleSelect(child.props.value, child.props.children as string)}
                  >
                    {child.props.children}
                  </div>
                );
              }
              return child;
            })}
          </div>
        </div>
      )}
    </div>
  );
};

const SelectTrigger: React.FC<SelectTriggerProps> = ({ children, className = '' }) => {
  return <div className={className}>{children}</div>;
};

const SelectContent: React.FC<SelectContentProps> = ({ children, className = '' }) => {
  return <div className={`p-1 ${className}`}>{children}</div>;
};

const SelectItem: React.FC<Omit<SelectItemProps, 'value'>> = ({ 
  children, 
  className = '',
  onClick
}) => {
  return (
    <div
      className={`relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground ${className}`}
      onClick={onClick}
    >
      {children}
    </div>
  );
};

const SelectValue: React.FC<SelectValueProps> = ({ placeholder, children }) => {
  return <span>{children || placeholder}</span>;
};

export { Select, SelectContent, SelectItem, SelectTrigger, SelectValue }; 