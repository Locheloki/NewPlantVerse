/**
 * Plant Presets - Common fruits and vegetables with care frequencies
 * Based on tropical/Philippine growing conditions
 * 
 * watering_frequency_days: How often to water (in days)
 * fertilizing_frequency_days: How often to fertilize (in days)
 */

const plantPresets = [
  // Vegetables - Daily watering
  {
    id: "tomato",
    name: "Tomato",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },
  {
    id: "chili",
    name: "Chili (Sili)",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },
  {
    id: "eggplant",
    name: "Eggplant (Talong)",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },
  {
    id: "okra",
    name: "Okra",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },
  {
    id: "kangkong",
    name: "Kangkong (Water Spinach)",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 21
  },
  {
    id: "pechay",
    name: "Pechay (Bok Choy)",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 21
  },
  {
    id: "stringbeans",
    name: "String Beans (Sitaw)",
    category: "Vegetables",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },

  // Fruits - Various watering frequencies
  {
    id: "papaya",
    name: "Papaya",
    category: "Fruits",
    watering_frequency_days: 2,
    fertilizing_frequency_days: 21
  },
  {
    id: "banana",
    name: "Banana",
    category: "Fruits",
    watering_frequency_days: 3,
    fertilizing_frequency_days: 21
  },
  {
    id: "calamansi",
    name: "Calamansi",
    category: "Fruits",
    watering_frequency_days: 3,
    fertilizing_frequency_days: 21
  },
  {
    id: "watermelon",
    name: "Watermelon",
    category: "Fruits",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  },
  {
    id: "squash",
    name: "Squash (Kalabasa)",
    category: "Fruits",
    watering_frequency_days: 1,
    fertilizing_frequency_days: 14
  }
];

/**
 * Initialize plant preset dropdown functionality
 * Automatically fills watering and fertilizing frequency fields on selection
 */
function initPlantPresets() {
  const plantPresetSelect = document.getElementById('plant_preset');
  
  if (!plantPresetSelect) {
    console.warn('plant_preset select element not found. Retrying...');
    setTimeout(initPlantPresets, 500);
    return;
  }

  console.log('Initializing plant presets...');
  
  // Populate the dropdown with plant options
  populatePlantDropdown();

  // Listen for changes on the plant preset dropdown
  plantPresetSelect.addEventListener('change', function() {
    const selectedPlantId = this.value;
    
    if (!selectedPlantId) {
      // Clear fields if no selection
      clearFrequencyFields();
      return;
    }

    // Find the selected plant
    const selectedPlant = plantPresets.find(plant => plant.id === selectedPlantId);
    
    if (selectedPlant) {
      fillFrequencyFields(selectedPlant);
    }
  });
}

// Try to initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPlantPresets);
} else {
  // DOM is already ready
  initPlantPresets();
}

/**
 * Populate the plant preset dropdown with options
 */
function populatePlantDropdown() {
  const plantPresetSelect = document.getElementById('plant_preset');
  
  if (!plantPresetSelect) return;

  // Clear existing options (except placeholder)
  plantPresetSelect.innerHTML = '<option value="">-- Select a Plant --</option>';

  // Group plants by category
  const categories = {};
  plantPresets.forEach(plant => {
    if (!categories[plant.category]) {
      categories[plant.category] = [];
    }
    categories[plant.category].push(plant);
  });

  // Create optgroups for each category
  Object.keys(categories).sort().forEach(category => {
    const optgroup = document.createElement('optgroup');
    optgroup.label = category;

    categories[category].forEach(plant => {
      const option = document.createElement('option');
      option.value = plant.id;
      option.textContent = plant.name;
      optgroup.appendChild(option);
    });

    plantPresetSelect.appendChild(optgroup);
  });
}

/**
 * Fill watering and fertilizing frequency input fields
 * @param {Object} plant - The selected plant object
 */
function fillFrequencyFields(plant) {
  const wateringField = document.getElementById('water_frequency');
  const fertilizingField = document.getElementById('fertilize_frequency');

  if (wateringField) {
    wateringField.value = plant.watering_frequency_days;
  }

  if (fertilizingField) {
    fertilizingField.value = plant.fertilizing_frequency_days;
  }
}

/**
 * Clear frequency input fields
 */
function clearFrequencyFields() {
  const wateringField = document.getElementById('water_frequency');
  const fertilizingField = document.getElementById('fertilize_frequency');

  if (wateringField) {
    wateringField.value = '';
  }

  if (fertilizingField) {
    fertilizingField.value = '';
  }
}
