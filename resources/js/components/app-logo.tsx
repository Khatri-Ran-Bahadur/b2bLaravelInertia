export default function AppLogo() {
    const AppName = 'B2B Application';
    const firstLetter = AppName.charAt(0).toUpperCase();
    return (
        <>
            <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
                {firstLetter}
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-none font-semibold">B2B Application</span>
            </div>
        </>
    );
}
