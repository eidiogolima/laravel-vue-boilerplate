export function calculateNiceMaximum(value: number, tickCount = 4): number {
    if (value <= 0) {
        return tickCount;
    }

    const roughStep = value / tickCount;
    const magnitude = 10 ** Math.floor(Math.log10(roughStep));
    const normalizedStep = roughStep / magnitude;
    const niceSteps = [1, 2, 2.5, 5, 10];
    const niceStep =
        niceSteps.find((step) => normalizedStep <= step) ?? niceSteps[4];

    return niceStep * magnitude * tickCount;
}

export function buildTickValues(maximum: number, tickCount = 4): number[] {
    return Array.from(
        { length: tickCount + 1 },
        (_, tickIndex) => (maximum / tickCount) * tickIndex,
    );
}
