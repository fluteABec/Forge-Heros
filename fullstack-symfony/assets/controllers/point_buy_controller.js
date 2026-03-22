import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['remaining', 'status'];

    static values = {
        budget: Number,
        base: Number,
    };

    connect() {
        this.boundUpdatePoints = this.updatePoints.bind(this);
        this.statInputs.forEach((input) => {
            input.addEventListener('input', this.boundUpdatePoints);
            input.addEventListener('change', this.boundUpdatePoints);
        });

        this.updatePoints();
    }

    disconnect() {
        if (!this.boundUpdatePoints) {
            return;
        }

        this.statInputs.forEach((input) => {
            input.removeEventListener('input', this.boundUpdatePoints);
            input.removeEventListener('change', this.boundUpdatePoints);
        });
    }

    updatePoints() {
        const totalCost = this.statInputs.reduce((total, input) => {
            const parsedValue = Number.parseInt(input.value, 10);
            const value = Number.isNaN(parsedValue) ? this.baseValue : parsedValue;

            return total + (value - this.baseValue);
        }, 0);

        const remaining = this.budgetValue - totalCost;

        this.remainingTarget.textContent = String(remaining);

        if (remaining < 0) {
            this.remainingTarget.classList.add('is-invalid');
            this.statusTarget.classList.add('is-invalid');
            this.statusTarget.textContent = 'La repartition depasse 27 points.';
            return;
        }

        this.remainingTarget.classList.remove('is-invalid');
        this.statusTarget.classList.remove('is-invalid');

        if (remaining === 0) {
            this.statusTarget.textContent = 'Tous les points ont ete utilises.';
            return;
        }

        this.statusTarget.textContent = 'Il reste encore des points a repartir.';
    }

    get statInputs() {
        return [
            this.element.querySelector('input[id$="_strength"]'),
            this.element.querySelector('input[id$="_dexterity"]'),
            this.element.querySelector('input[id$="_constitution"]'),
            this.element.querySelector('input[id$="_intelligence"]'),
            this.element.querySelector('input[id$="_wisdom"]'),
            this.element.querySelector('input[id$="_charisma"]'),
        ].filter((input) => input !== null);
    }
}
