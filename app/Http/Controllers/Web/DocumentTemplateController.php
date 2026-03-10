<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::orderBy('type')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('document-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('document-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,quote,reminder',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo_position' => 'required|in:left,center,right',
            'color_primary' => 'required|string|max:7',
            'color_secondary' => 'required|string|max:7',
            'show_logo' => 'boolean',
            'show_payment_info' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['show_logo'] = $request->boolean('show_logo');
        $validated['show_payment_info'] = $request->boolean('show_payment_info');
        $validated['is_default'] = $request->boolean('is_default');

        $template = DocumentTemplate::create($validated);

        if ($template->is_default) {
            $template->setAsDefault();
        }

        return redirect()->route('document-templates.index')->with('success', 'Modèle créé avec succès.');
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        return view('document-templates.edit', ['template' => $documentTemplate]);
    }

    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,quote,reminder',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo_position' => 'required|in:left,center,right',
            'color_primary' => 'required|string|max:7',
            'color_secondary' => 'required|string|max:7',
            'show_logo' => 'boolean',
            'show_payment_info' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['show_logo'] = $request->boolean('show_logo');
        $validated['show_payment_info'] = $request->boolean('show_payment_info');
        $validated['is_default'] = $request->boolean('is_default');

        $documentTemplate->update($validated);

        if ($documentTemplate->is_default) {
            $documentTemplate->setAsDefault();
        }

        return redirect()->route('document-templates.index')->with('success', 'Modèle mis à jour avec succès.');
    }

    public function destroy(DocumentTemplate $documentTemplate)
    {
        if ($documentTemplate->is_default) {
            return back()->with('error', 'Impossible de supprimer le modèle par défaut.');
        }

        $documentTemplate->delete();

        return redirect()->route('document-templates.index')->with('success', 'Modèle supprimé avec succès.');
    }

    public function setDefault(DocumentTemplate $documentTemplate)
    {
        $documentTemplate->setAsDefault();

        return back()->with('success', 'Modèle défini par défaut.');
    }
}
