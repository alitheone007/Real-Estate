<?php
class StatsCards {
    private $stats;

    public function __construct() {
        $this->stats = [
            [
                'title' => 'Total Leads',
                'value' => $this->get_total_leads(),
                'change' => 12.5,
                'changeTypeype' => 'increase'
            ],
            // Add more stats
        ];
    }

    private function get_total_leads() {
        // Get leads count from WordPress custom post type
        return wp_count_posts('property_lead')->publish;
    }

    public function render() {
        ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php foreach ($this->stats as $stat): ?>
                <div class="glass-card animate-fade-in hover-scale p-4">
                    <!-- Stat card content -->
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
