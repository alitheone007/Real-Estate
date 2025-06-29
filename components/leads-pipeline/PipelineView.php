<?php
class PipelineView {
    private $stages = [
        'new' => ['label' => 'New', 'color' => 'blue'],
        'contacted' => ['label' => 'Contacted', 'color' => 'cyan'],
        'qualified' => ['label' => 'Qualified', 'color' => 'green'],
        // Add more stages
    ];

    public function render() {
        ?>
        <div class="leads-pipeline">
            <?php foreach ($this->stages as $key => $stage): ?>
                <div class="pipeline-stage">
                    <div class="stage-header">
                        <h3><?php echo $stage['label']; ?></h3>
                        <span class="count"><?php echo $this->get_stage_count($key); ?></span>
                    </div>
                    <?php $this->render_stage_leads($key); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
