import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import {
    CheckIcon,
    XCircle,
    ChevronDown,
    XIcon,
    WandSparkles,
} from "lucide-react";

import { cn } from "@/lib/utils";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
} from "@/components/ui/command";

/**
 * Variants for the multi-select component to handle different styles.
 * Uses class-variance-authority (cva) to define different styles based on "variant" prop.
 */
const multiSelectVariants = cva(
    "tw:m-1 tw:transition tw:ease-in-out tw:delay-150 tw:hover:-translate-y-1 tw:hover:scale-110 tw:duration-300",
    {
        variants: {
            variant: {
                default:
                    "tw:border-foreground/10 tw:text-foreground tw:bg-card tw:hover:bg-card/80",
                secondary:
                    "tw:border-foreground/10 tw:bg-secondary tw:text-secondary-foreground tw:hover:bg-secondary/80",
                destructive:
                    "tw:border-transparent tw:bg-destructive tw:text-destructive-foreground tw:hover:bg-destructive/80",
                inverted: "tw:inverted",
            },
        },
        defaultVariants: {
            variant: "default",
        },
    }
);

/**
 * Props for MultiSelect component
 */
interface MultiSelectProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof multiSelectVariants> {
    /**
     * An array of option objects to be displayed in the multi-select component.
     * Each option object has a label, value, and an optional icon.
     */
    options: {
        /** The text to display for the option. */
        label: string;
        /** The unique value associated with the option. */
        value: string;
        /** Optional icon component to display alongside the option. */
        icon?: React.ComponentType<{ className?: string }>;
    }[];

    /**
     * Callback function triggered when the selected values change.
     * Receives an array of the new selected values.
     */
    onValueChange: (value: string[]) => void;

    /** The default selected values when the component mounts. */
    defaultValue?: string[];

    /**
     * Placeholder text to be displayed when no values are selected.
     * Optional, defaults to "Select options".
     */
    placeholder?: string;

    /**
     * Animation duration in seconds for the visual effects (e.g., bouncing badges).
     * Optional, defaults to 0 (no animation).
     */
    animation?: number;

    /**
     * Maximum number of items to display. Extra selected items will be summarized.
     * Optional, defaults to 3.
     */
    maxCount?: number;

    /**
     * The modality of the popover. When set to true, interaction with outside elements
     * will be disabled and only popover content will be visible to screen readers.
     * Optional, defaults to false.
     */
    modalPopover?: boolean;

    /**
     * If true, renders the multi-select component as a child of another component.
     * Optional, defaults to false.
     */
    asChild?: boolean;

    /**
     * Additional class names to apply custom styles to the multi-select component.
     * Optional, can be used to add custom styles.
     */
    className?: string;
}

export const MultiSelect = React.forwardRef<
    HTMLButtonElement,
    MultiSelectProps
>(
    (
        {
            options,
            onValueChange,
            variant,
            defaultValue = [],
            placeholder = "Select options",
            animation = 0,
            maxCount = 3,
            modalPopover = false,
            asChild = false,
            className,
            ...props
        },
        ref
    ) => {
        const [selectedValues, setSelectedValues] =
            React.useState<string[]>(defaultValue);
        const [isPopoverOpen, setIsPopoverOpen] = React.useState(false);
        const [isAnimating, setIsAnimating] = React.useState(false);

        const handleInputKeyDown = (
            event: React.KeyboardEvent<HTMLInputElement>
        ) => {
            if (event.key === "Enter") {
                setIsPopoverOpen(true);
            } else if (event.key === "Backspace" && !event.currentTarget.value) {
                const newSelectedValues = [...selectedValues];
                newSelectedValues.pop();
                setSelectedValues(newSelectedValues);
                onValueChange(newSelectedValues);
            }
        };

        const toggleOption = (option: string) => {
            const newSelectedValues = selectedValues.includes(option)
                ? selectedValues.filter((value) => value !== option)
                : [...selectedValues, option];
            setSelectedValues(newSelectedValues);
            onValueChange(newSelectedValues);
        };

        const handleClear = () => {
            setSelectedValues([]);
            onValueChange([]);
        };

        const handleTogglePopover = () => {
            setIsPopoverOpen((prev) => !prev);
        };

        const clearExtraOptions = () => {
            const newSelectedValues = selectedValues.slice(0, maxCount);
            setSelectedValues(newSelectedValues);
            onValueChange(newSelectedValues);
        };

        const toggleAll = () => {
            if (selectedValues.length === options.length) {
                handleClear();
            } else {
                const allValues = options.map((option) => option.value);
                setSelectedValues(allValues);
                onValueChange(allValues);
            }
        };

        return (
            <Popover
                open={isPopoverOpen}
                onOpenChange={setIsPopoverOpen}
                modal={modalPopover}
            >
                <PopoverTrigger asChild>
                    <Button
                        ref={ref}
                        {...props}
                        onClick={handleTogglePopover}
                        className={cn(
                            "tw:flex tw:w-full tw:p-1 tw:rounded-md tw:border tw:min-h-10 tw:h-auto tw:items-center tw:justify-between tw:bg-inherit tw:hover:bg-inherit tw:[&_svg]:pointer-events-auto",
                            className
                        )}
                    >
                        {selectedValues.length > 0 ? (
                            <div className="tw:flex tw:justify-between tw:items-center tw:w-full">
                                <div className="tw:flex tw:flex-wrap tw:items-center">
                                    {selectedValues.slice(0, maxCount).map((value) => {
                                        const option = options.find((o) => o.value === value);
                                        const IconComponent = option?.icon;
                                        return (
                                            <Badge
                                                key={value}
                                                className={cn(
                                                    isAnimating ? "tw:animate-bounce" : "",
                                                    multiSelectVariants({ variant })
                                                )}
                                                style={{ animationDuration: `${animation}s` }}
                                            >
                                                {IconComponent && (
                                                    <IconComponent className="tw:h-4 tw:w-4 tw:mr-2" />
                                                )}
                                                {option?.label}
                                                <XCircle
                                                    className="tw:ml-2 tw:h-4 tw:w-4 tw:cursor-pointer"
                                                    onClick={(event) => {
                                                        event.stopPropagation();
                                                        toggleOption(value);
                                                    }}
                                                />
                                            </Badge>
                                        );
                                    })}
                                    {selectedValues.length > maxCount && (
                                        <Badge
                                            className={cn(
                                                "tw:bg-transparent tw:text-foreground tw:border-foreground/1 tw:hover:bg-transparent",
                                                isAnimating ? "tw:animate-bounce" : "",
                                                multiSelectVariants({ variant })
                                            )}
                                            style={{ animationDuration: `${animation}s` }}
                                        >
                                            {`+ ${selectedValues.length - maxCount} more`}
                                            <XCircle
                                                className="tw:ml-2 tw:h-4 tw:w-4 tw:cursor-pointer"
                                                onClick={(event) => {
                                                    event.stopPropagation();
                                                    clearExtraOptions();
                                                }}
                                            />
                                        </Badge>
                                    )}
                                </div>
                                <div className="tw:flex tw:items-center tw:justify-between">
                                    <XIcon
                                        className="tw:h-4 tw:mx-2 tw:cursor-pointer tw:text-muted-foreground"
                                        onClick={(event) => {
                                            event.stopPropagation();
                                            handleClear();
                                        }}
                                    />
                                    <Separator
                                        orientation="vertical"
                                        className="tw:flex tw:min-h-6 tw:h-full"
                                    />
                                    <ChevronDown className="tw:h-4 tw:mx-2 tw:cursor-pointer tw:text-muted-foreground" />
                                </div>
                            </div>
                        ) : (
                            <div className="tw:flex tw:items-center tw:justify-between tw:w-full tw:mx-auto">
                <span className="tw:text-sm tw:text-muted-foreground tw:mx-3">
                  {placeholder}
                </span>
                                <ChevronDown className="tw:h-4 tw:cursor-pointer tw:text-muted-foreground tw:mx-2" />
                            </div>
                        )}
                    </Button>
                </PopoverTrigger>
                <PopoverContent
                    className="tw:w-auto tw:p-0"
                    align="start"
                    onEscapeKeyDown={() => setIsPopoverOpen(false)}
                >
                    <Command>
                        <CommandInput
                            placeholder="Search..."
                            onKeyDown={handleInputKeyDown}
                        />
                        <CommandList>
                            <CommandEmpty>No results found.</CommandEmpty>
                            <CommandGroup>
                                <CommandItem
                                    key="all"
                                    onSelect={toggleAll}
                                    className="tw:cursor-pointer"
                                >
                                    <div
                                        className={cn(
                                            "tw:mr-2 tw:flex tw:h-4 tw:w-4 tw:items-center tw:justify-center tw:rounded-sm tw:border tw:border-primary",
                                            selectedValues.length === options.length
                                                ? "tw:bg-primary tw:text-primary-foreground"
                                                : "tw:opacity-50 tw:[&_svg]:invisible"
                                        )}
                                    >
                                        <CheckIcon className="tw:h-4 tw:w-4" />
                                    </div>
                                    <span>(Select All)</span>
                                </CommandItem>
                                {options.map((option) => {
                                    const isSelected = selectedValues.includes(option.value);
                                    return (
                                        <CommandItem
                                            key={option.value}
                                            onSelect={() => toggleOption(option.value)}
                                            className="tw:cursor-pointer"
                                        >
                                            <div
                                                className={cn(
                                                    "tw:mr-2 tw:flex tw:h-4 tw:w-4 tw:items-center tw:justify-center tw:rounded-sm tw:border tw:border-primary",
                                                    isSelected
                                                        ? "tw:bg-primary tw:text-primary-foreground"
                                                        : "tw:opacity-50 tw:[&_svg]:invisible"
                                                )}
                                            >
                                                <CheckIcon className="tw:h-4 tw:w-4" />
                                            </div>
                                            {option.icon && (
                                                <option.icon className="tw:mr-2 tw:h-4 tw:w-4 tw:text-muted-foreground" />
                                            )}
                                            <span>{option.label}</span>
                                        </CommandItem>
                                    );
                                })}
                            </CommandGroup>
                            <CommandSeparator />
                            <CommandGroup>
                                <div className="tw:flex tw:items-center tw:justify-between">
                                    {selectedValues.length > 0 && (
                                        <>
                                            <CommandItem
                                                onSelect={handleClear}
                                                className="tw:flex-1 tw:justify-center tw:cursor-pointer"
                                            >
                                                Clear
                                            </CommandItem>
                                            <Separator
                                                orientation="vertical"
                                                className="tw:flex tw:min-h-6 tw:h-full"
                                            />
                                        </>
                                    )}
                                    <CommandItem
                                        onSelect={() => setIsPopoverOpen(false)}
                                        className="tw:flex-1 tw:justify-center tw:cursor-pointer tw:max-w-full"
                                    >
                                        Close
                                    </CommandItem>
                                </div>
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
                {animation > 0 && selectedValues.length > 0 && (
                    <WandSparkles
                        className={cn(
                            "tw:cursor-pointer tw:my-2 tw:text-foreground tw:bg-background tw:w-3 tw:h-3",
                            isAnimating ? "" : "tw:text-muted-foreground"
                        )}
                        onClick={() => setIsAnimating(!isAnimating)}
                    />
                )}
            </Popover>
        );
    }
);

MultiSelect.displayName = "MultiSelect";
